#!/usr/bin/python3
# -*- coding: UTF-8 -*-
import json
import os
import shutil
import requests
import re
import pymysql
import pymysql.cursors
import redis
import sys
import time
import random
import numpy as np
from dotenv import load_dotenv
from cmp_version import cmp_version
from qcloud_cos import CosConfig
from qcloud_cos import CosS3Client
from qcloud_cos import CosServiceError
from qcloud_cos import CosClientError

class CDN(object):
    def __init__(self):
        '''
        初始化
        '''
        # 加载环境变量
        load_dotenv(dotenv_path=self.getLocalPath('/../.env'))

        # 设置数据库
        self.setDB()

        # 设置缓存
        self.setRedis()

        # 设置文件存储
        self.setStorage()

        # 设置文件存储路径
        self.setStoragePath()

        # 设置下载前缀
        self.setDownloadPrefix()

        # 创建临时文件夹
        self.createTmpFolder()

    def run(self):
        '''
        '''
        # 是否重新处理之前失败
        if bool(os.getenv('NOT_RETRY', False)) == False:
            # 获取失败Package列表
            failList = self.getFailList()

            for item in failList:
                name = item['name']
                version = item['version']

                librarie = self.getLibrarie(item)

                if not librarie:
                    continue

                itemVersionFiles = self.getLibrarieVersionFiles(librarie, version)

                if not itemVersionFiles:
                    continue

                if self.hasMinVersion(version, item['minversion']):
                    continue

                self.setStartTime()

                files = item['fail_files'].split(',')

                failFiles = []

                for file in files:
                    if not self.downloadAndUpload(name, item['alias'], version, file):
                        failFiles.append(file)
                        continue

                if len(failFiles) == 0:
                    self.createPackageVersion(name, version, itemVersionFiles)

                self.setEndTime()

                self.updatePackageLog(item['id'], version, files, failFiles)

        packages = self.getPackagesList()

        if not packages:
            return

        for package in packages:
            name = package['name']
            minversion = package['minversion']

            librarie = self.getLibrarie(package)

            if not librarie:
                continue

            assets = reversed(librarie['assets'])

            for versionItem in assets:
                # 版本号
                version = versionItem['version']

                self.setStartTime()

                # 当前版本是否存在
                if self.isVersionExist(name, version) or self.hasMinVersion(version, minversion):
                    continue

                # 文件列表
                files   = list(versionItem['files'])

                # 失败文件列表
                failFiles = []

                for file in files:
                    if not self.downloadAndUpload(name, package['alias'], version, file):
                        failFiles.append(file)
                        continue

                if len(failFiles) == 0:
                    self.createPackageVersion(name, version, files)

                self.setEndTime()
                self.createPackageLog(package['id'], version, files, failFiles)
        pass

    def setDB(self):
        '''
        设置数据库
        '''
        self.db = pymysql.connect(
            host=os.getenv('DB_HOST'),
            port=int(os.getenv('DB_PORT')),
            user=os.getenv('DB_USERNAME'),
            password=os.getenv('DB_PASSWORD'),
            db=os.getenv('DB_DATABASE'),
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor)

        self.cursor = self.db.cursor()

    def setRedis(self):
        '''
        设置Redis
        '''
        self.redis = redis.StrictRedis(
            host=os.getenv('REDIS_HOST'),
            password=os.getenv('REDIS_PASSWORD'),
            port=os.getenv('REDIS_PORT', 6379),
            db=os.getenv('REDIS_DB', 0))

    def setStorage(self):
        '''
        设置存储
        '''
        self.storage = CosS3Client(CosConfig(
            Secret_id=os.getenv('STORAGE_SECRET_ID'),
            Secret_key=os.getenv('STORAGE_SECRET_KEY'),
            Region=os.getenv('STORAGE_REGION')))

    def setDownloadPrefix(self):
        '''
        设置下载链接前缀
        '''
        self.downloadPrefix = os.getenv('DOWNLOAD_PREFIX', 'https://cdnjs.loli.net/ajax/libs/%s/%s/%s')

    def setStoragePath(self):
        '''
        设置云存储路径
        '''
        self.storagePath = os.getenv('STORAGE_PATH', '/').replace('\\', '/')

        if not self.storagePath[-1] == '/':
            self.storagePath = self.storagePath + '/'

    def setStartTime(self):
        self.startTime = time.time()

    def setEndTime(self):
        self.endTime = time.time()

    def getLocalPath(self, path):
        '''
        获取本地路径
        Parameters:
            path 路径
        '''
        return os.path.dirname(__file__) + path

    def getLibrarie(self, package):
        '''
        获取Librarie
        Parameters:
            package 包信息
        '''
        name = package['name']

        if 'libraries' in dir(self):
            if name in self.libraries:
                return self.libraries[name]
        else:
            self.libraries = {}

        url = 'https://api.cdnjs.com/libraries/%s' % name

        head = requests.head(url)

        etag = head.headers['Etag']

        path = self.getLibrariePath(name)

        hasDownload = False

        if os.path.exists(path):
            if package['etag'] == None or etag != package['etag']:
                hasDownload = True
        else:
            hasDownload = True

        if hasDownload == True:
            librarie = self.requests(url)

            if isinstance(librarie, int) == True or len(librarie.json()) == 0:
                self.log('获取API数据失败[%s][%s]', name, package['alias'])
                return False

            librarie = librarie.json()

            fh = open(path, 'w')
            fh.write(json.dumps(librarie))
            fh.close()
        else:
            librarie = json.load(open(path, encoding="utf-8"))

        self.libraries[name] = librarie

        if etag != package['etag']:
            self.updatePackageEtag(name, etag)

        return librarie

    def getLibrariePath(self, name):
        '''
        设置Librarie存储路径
        '''
        return self.getLocalPath('/../%s/%s.json' % (os.getenv('PYTHON_PATH', 'python/libraries'), name))

    def getLibrarieVersionFiles(self, librarie, version):
        '''
        获取版本文件列表
        Parameters:
            librarie 包数据
            version 版本
        '''
        for item in librarie['assets']:
            if item['version'] == version:
                return item['files']

        return []

    def isVersionStorageExist(self, name, version):
        '''
        存储是否存在版本
        Parameters:
            name 名称
            version: 版本
        '''
        return False

    def isVersionExist(self, name, version):
        '''
        是否存在版本
        Parameters:
            name 名称
            version: 版本
        '''
        return self.redis.hexists('libraries:' + name, version)

    def hasMinVersion(self, version, minVersion):
        '''
        是否低于最低版本
        Parameters:
            version： 当前版本
            minVersion: 最低版本
        '''
        if minVersion == None:
            return False

        return cmp_version(version, minVersion) == -1
    
    def downloadAndUpload(self, name, alias, version, file):
        '''
        下载并上传
        Parameters:
            name 包名称
            alias 包别名
            version 版本
            file 下载文件路径
        '''
        # 下载链接
        downloadUrl = self.downloadPrefix % (name, version, file)

        # 下载保存路径
        downloadPath = '%s%s-%s-%s' % (self.getTmpFolder(), name, version, file.replace('/', '-'))

        # 下载
        if self.downloadFile(downloadUrl, downloadPath) != True:
            return False

        # 上传文件路径
        uploadPath = '%s%s/%s/%s' % (self.storagePath, alias, version, file)

        # 上传
        if self.uploadFile(uploadPath, downloadPath) != True:
            return False
        
        self.deleteFile(downloadPath)
        return True

    def downloadFile(self, url, path):
        '''
        下载文件
        Parameters:
            url 下载链接
            path 本地路径
        '''
        request = self.requests(url)

        if isinstance(request, int) == True:
            self.log(url + '下载失败')
            return False

        with open(path, 'wb') as fh:
            try:
                for chunk in request.iter_content(1024 * 1024):
                    fh.write(chunk)
            except (requests.exceptions.ChunkedEncodingError, requests.exceptions.ConnectionError) as e:
                fh.close()
        return True

    def uploadFile(self, file, path):
        '''
        上传文件
        Parameters:
            file 上传路径
            path 本地路径
        '''
        try:
            r = self.storage.upload_file(
            Bucket=os.getenv('STORAGE_BUCKET'),
            LocalFilePath=path,
            Key=file,
            PartSize=10,
            MAXThread=10)
        except (CosClientError, CosServiceError) as e:
            self.log(file + '上传失败')
            return False
        return True

    def deleteFile(self, path):
        '''
        删除文件
        Parameters:
            path 路径
        '''
        return os.remove(path)

    def getTmpFolder(self):
        '''
        获取临时文件夹路径
        '''
        return self.getLocalPath('/tmp/')

    def createTmpFolder(self):
        '''
        创建临时目录
        '''
        self.deleteTmpFolder()
        os.mkdir(self.getTmpFolder())

    def deleteTmpFolder(self):
        '''
        删除临时目录
        '''
        if os.path.exists(self.getTmpFolder()):
            shutil.rmtree(self.getTmpFolder())

    def getPackagesList(self):
        '''
        获取Packages列表
        '''
        sql =   "SELECT id, name, alias, minversion, etag FROM packages WHERE visible = 1 AND IFNULL(deleted_at,'0') = '0'"

        try:
            self.cursor.execute(sql)

            return self.cursor.fetchall()
        except:
            self.log('连接数据库失败')
        return False

    def getFailList(self):
        '''
        获取失败列表
        '''
        sql = 'SELECT `packages_logs`.`id`, `packages_logs`.`pid`, `packages`.`name`, `packages`.`alias`, `packages`.`minversion`, `packages`.`etag`, `packages_logs`.`version`, `packages_logs`.`fail_files` FROM `packages_logs` INNER JOIN `packages` WHERE `packages_logs`.`pid` = `packages`.`id` AND `packages_logs`.`fail_number` != 0 AND `packages_logs`.`status` != 1 AND `packages`.`visible` = 1'

        try:
            self.cursor.execute(sql)

            return self.cursor.fetchall()
        except:
            self.log('连接数据库失败')
        return False

    def createPackageVersion(self, name, version, files):
        '''
        创建Package版本记录
        Parameters:
            name 名称
            version 版本
            files 文件列表
        '''
        return self.redis.hset('libraries:' + name, version, ','.join(files))

    def createPackageLog(self, id, version, files, failFiles):
        '''
        创建Package日志
        Parameters:
            id id
            version 版本
            files 全部文件
            failFiles 失败文件
        '''
        filesLen = len(files)
        failFilesLen = len(failFiles)

        sql =   """
                INSERT INTO packages_logs (pid, version, total_number, success_number, fail_number, fail_files, running_time, status, created_at, updated_at) VALUES (%d, '%s', %d, %d, %d, '%s', %.3f, %d, "%s", "%s")
                """

        sql = sql % (id, str(version), filesLen, filesLen - failFilesLen, failFilesLen, ','.join(failFiles), self.endTime - self.startTime, 0 if failFilesLen else 1, self.normalizeTime(self.startTime), self.normalizeTime(self.endTime))

        self.db.ping(reconnect=True)
        self.cursor.execute(sql)
        self.db.commit()

        if not self.cursor.rowcount:
            self.log('插入日志数据失败[%s][%s]' % (id, version))
            return False
        return True

    def updatePackageLog(self, id, version, files, failFiles):
        '''
        更新Package日志
        Parameters:
            id id
            version 版本
            files 全部文件
            failFiles 失败文件
        '''
        filesLen = len(files)
        failFilesLen = len(failFiles)

        sql = 'UPDATE `packages_logs` SET success_number=+%d, fail_number=%d, fail_files="%s", running_time=+%.3f, status=%d, updated_at="%s" WHERE id=%d'

        sql = sql % (filesLen - failFilesLen, failFilesLen, ','.join(failFiles), self.endTime - self.startTime, 0 if failFilesLen else 1, self.normalizeTime(self.endTime), id)

        self.db.ping(reconnect=True)
        self.cursor.execute(sql)
        self.db.commit()

        if not self.cursor.rowcount:
            self.log('更新日志数据失败[%s][%s]' % (id, version))
            return False
        return True

    def updatePackageEtag(self, name, etag):
        '''
        更新Etag
        Parameters:
            name 名称
            etag Etag
        '''
        sql =   '''UPDATE packages SET etag='%s' WHERE name='%s'
                ''' % (etag, name)

        try:
            self.db.ping(reconnect=True)
            self.cursor.execute(sql)
            self.db.commit()
        except:
            self.db.rollback()

        return True

    def normalizeTime(self, timestamp):
        '''
        格式化时间
        Parameters:
            timestamp 时间戳
        '''
        return time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(timestamp))

    def log(self, msg):
        print(msg)

    def requests(self, url, num = 5):
        try:
            headers = {
                'user-agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1'
            }
            r = requests.get(url, headers=headers, timeout=5, stream=True)
            r.raise_for_status()
        except (requests.exceptions.ConnectTimeout, requests.exceptions.Timeout,
        requests.exceptions.HTTPError, requests.exceptions.ProxyError) as e:
            return 0 if num == 1 else self.requests(url, num - 1)
        else:
            return r


if __name__ == '__main__':
    cdn = CDN()
    cdn.run()
