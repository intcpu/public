# -*- coding: UTF-8 -*-
from urllib.request import Request, urlopen
from urllib.error import URLError
import math,json
import datetime,time
import os,platform
import numpy as np
from huigui import *
from fennei import *
from liuyao import *
from pymongo import MongoClient

#一天执行一次

def mongondb(host = 'localhost',port = 27017):
	client = MongoClient(host, port)
	db = client.ripple  #读取ripple库
	daylog = db.daylog  #读取daylog集合
	return daylog


#读取文件
def readFile(fileName):
	if(sysstr =="Windows"):
		file = open('datas/'+fileName)
	else:
		file = open('/www/pyshell/datas/'+fileName)
	
	html = file.read()
	file.close()
	data = json.loads(html)
	return data

# 遍历指定目录，将目录下的文件存入mongo
def fileData(sign):
	for file in sign:
		file    =  str(file)
		if(sysstr =="Windows"):
			pathDir =  os.listdir('datas/')
		else:
			pathDir =  os.listdir('/www/pyshell/datas/')
		
		pathDir.sort()
		for fileName in pathDir:
			if fileName[0:10] == today:
				break
			if fileName[11:12] == file:
				data = readFile(fileName)
				if data['result'] == 'success':
					daylog.insert(data['exchanges'])

#从数据中获取变量矩阵与结果矩阵,预测下一阶段结果
def dataToMatrix():
	l = 0
	datas = {}
	for val in daylog.find():
		day = val['open_time'][0:10]
		if (day in datas) == False: datas[day] = []
		datas[day] += [{"close":val['close'],"high":val['high'],"low":val['low'],"open":val['open'],"vwap":val['vwap']}]

	col = 24
	row = len(datas)

	x_row = np.mat(np.zeros((row,col)))
	y_row = np.mat(np.zeros((row,1)))

	j = 0
	for val in datas:
		day_ave = []
		for v in datas[val]:
			day_ave += [v['vwap']]
		y_row[j,:] = np.mean(np.float64(day_ave))
		hour_num = int(len(day_ave)/col)

		i = 0
		k = 0
		hour_ave = []
		row_data = np.mat(np.zeros((1,col)))
		for v in day_ave:
			i += 1
			hour_ave += [v] 
			if i == hour_num:
				row_data[:,k] = np.mean(np.float64(hour_ave))
				i = 0
				hour_ave = []
				k += 1
				if k == col: break
		x_row[j,:] = row_data
		j += 1


	x = x_row[0:(row-2)]
	y = y_row[1:(row-1)]
	last = x_row[row-1]
	print(last)
	return x,y,last

#json接口请求
def jsonInterfaceRequest(url,i):
	req = Request(url)
	try:
	    response = urlopen(req)
	except URLError as e:
	    if hasattr(e, 'reason'):
	        print('Reason: ', e.reason)
	    elif hasattr(e, 'code'):
	        print('Error code: ', e.code)
	else:
		html = response.read()
		# if(sysstr =="Windows"):
		# 	file = open('datas/'+today+'-'+str(i)+'.txt', 'wb')
		# else:
		# 	file = open('/www/pyshell/datas/'+today+'-'+str(i)+'.txt', 'wb')
		
		# file.write(html)
		# file.close()
		data = json.loads(html)

		if data['result'] == 'success':
			daylog.insert(data['exchanges'])

		

#所有外部接口
def dataWork():
	urls = ['https://data.ripple.com/v2/exchanges/XRP/CNY+razqQKzJRdB4UxFPWf5NEpEG3WMkmwgcXA?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/CNY+rKiCet8SdvWxPXnAgYarFUXMh1zCPz432Y?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/CNY+rPT74sUcTBTQhkHVD54WGncoqXEAMYbmH7?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/USD+rvYAfWj5gh67oV6fW32ZzP3Aw4Eubs59B?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/USD+rhub8VRN55s94qWKDv6jmDy1pUykJzF3wq?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/ETH+rcA8X3TVMST1n3CJeAdGk1RdRCHii7N2h/XRP?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/BTC+rchGBxcD1A1C2tdxF6papQYZ8kjRKMYcL/XRP?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59']
	i = 1
	for url in urls:
		jsonInterfaceRequest(url,i)
		time.sleep(1)
		i = i + 1
		break
	return

if __name__ == "__main__":
	sysstr = platform.system()
	doTime = str(datetime.datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S'))
	localTime = str(datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
	today = str(datetime.datetime.utcnow().strftime('%Y-%m-%d'))  #str(today - datetime.timedelta(days))

	daylog = mongondb()

	#dataWork()

	#fileData([2]);
	#
	
	x,y,last = dataToMatrix()
	#theta 	 = normalEqn(x,y)
	#nextPrice = last@theta
	nextPrice 	 = gradientDescentResult(x,y,0.00000049,5000,last)
	
	shu,bian = qushu(nextPrice)
	print(quyao(shu,bian))
	print(nextPrice)
	

