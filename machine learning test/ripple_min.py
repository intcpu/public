# -*- coding: UTF-8 -*-
from urllib.request import Request, urlopen
from urllib.error import URLError
import math,json
import datetime,time
from threading import Timer
import os,platform
import numpy as np;
import matplotlib.pyplot as plt
import scipy.optimize as opt
from sklearn import linear_model

#回归:多变量代价函数
def computeCost(X, y, theta):
	inner = np.power(((X * theta.T) - y), 2)
	return np.sum(inner) / (2 * len(X))

#回归:多变量梯度下降
#
#  X = np.matrix(X.values)  # 5x2的矩阵
#  y = np.matrix(y.values)  # 5x1的矩阵
#  theta = np.matrix(np.array([0,0]))  # 1x2的zero矩阵(自定义)
#  #可以看下代价函数结果
#  computeCost(X, y, theta)
#  
#  #设置 alpha学习速率   iters迭代次数
#  alpha = 0.01
#  iters = 1000
#  g, cost = gradientDescent(X, y, theta, alpha, iters)
#
def gradientDescent(X, y, theta, alpha, iters):
	temp = np.matrix(np.zeros(theta.shape))
	parameters = int(theta.ravel().shape[1])
	cost = np.zeros(iters)

	for i in range(iters):
		error = (X * theta.T) - y

		for j in range(parameters):
			term = np.multiply(error, X[:,j])
			temp[0,j] = theta[0,j] - ((alpha / len(X)) * np.sum(term))

		theta = temp
		cost[i] = computeCost(X, y, theta)
	return theta, cost

#回归：theta正规方程求解
def normalEqn(X,y):
	theta = np.linalg.inv(X.T@X)@X.T@y
	return theta
 
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

# 遍历指定目录，显示目录下的所有文件名
def fileData(sign):
	datas   =  []
	for file in sign:
		file    =  str(file)
		if(sysstr =="Windows"):
			pathDir =  os.listdir('datas/')
		else:
			pathDir =  os.listdir('/www/pyshell/datas/')
		
		pathDir.sort()
		for fileName in pathDir:
			if fileName[11:12] == file:
				data = readFile(fileName)
				if data['result'] == 'success':
					datas += data['exchanges']
	return datas

#从数据中获取变量矩阵与结果矩阵,预测下一阶段结果
def dataToMatrix(datas):
	l = len(datas)-2
	x = np.mat(np.zeros((l,5))) 
	y = np.mat(np.zeros((l,1)))
	z = np.mat(np.zeros((l,1)))
	x[0,:] = [1,2,3,4,5]
	i = 0
	for val in datas:
		#这个数据梯度下降算不准
		#x[i,:] = [val['base_volume'],val['buy_volume'],val['close'],val['count'],val['counter_volume'],val['high'],val['low'],val['open'],val['vwap']]
		x[i,:] = [val['close'],val['high'],val['low'],val['open'],val['vwap']]
		y[i,:] = datas[i+1]['close']
		z[i,:] = 1 if datas[i+1]['close'] > val['close'] else 0 
		i = i+1
		if i == l:
			#这个数据梯度下降算不准
			#lastData  = np.mat(np.float64([datas[i]['base_volume'],datas[i]['buy_volume'],datas[i]['close'],datas[i]['count'],datas[i]['counter_volume'],datas[i]['high'],datas[i]['low'],datas[i]['open'],datas[i]['vwap']]))
			#nowData  = np.mat(np.float64([datas[i+1]['base_volume'],datas[i+1]['buy_volume'],datas[i+1]['close'],datas[i+1]['count'],datas[i+1]['counter_volume'],datas[i+1]['high'],datas[i+1]['low'],datas[i+1]['open'],datas[i+1]['vwap']]))
			
			lastData  = np.mat(np.float64([datas[i]['close'],datas[i]['high'],datas[i]['low'],datas[i]['open'],datas[i]['vwap']]))
			nowData  = np.mat(np.float64([datas[i+1]['close'],datas[i+1]['high'],datas[i+1]['low'],datas[i]['open'],datas[i]['vwap']]))
			lastPrice = datas[i]['close']
			lastTime  = datas[i]['close_time']
			nowPrice  = datas[i+1]['close']
			nowTime   = datas[i+1]['close_time']			
			break
	return x,y,z,lastData,nowData,lastTime,lastPrice,nowTime,nowPrice

#正规方程预测结果
def normalEqnResult(x,y,lastData,nowData):
	theta 	 = normalEqn(x,y)
	nextPrice = lastData@theta
	willPrice = nowData@theta
	return str(nextPrice),str(willPrice)


#缩放特征，特征归一化，谨慎使用
def zoomFeature(data):
	data = (data - data.mean())/data.std()
	return data

#梯度下降预测结果
def gradientDescentResult(x,y,lastData,nowData):
	#x = zoomFeature(x)

	alpha = 0.00000000001
	iters = 6000
	theta = np.mat((np.zeros(x.shape[1]))) #初始化全部为0向量
	g, cost = gradientDescent(x, y, theta, alpha, iters)
	gradientDescentPrint(iters,cost)
	nextPrice = lastData@g.T
	willPrice = nowData@g.T
	return str(nextPrice),str(willPrice)

#梯度下降数据分布观察
def gradientDescentPrint(iters,cost):
	if(sysstr !="Windows"):
		return
	#不断的调试 alpha 与 iters 使迭代次数iters与误差cost图案逐渐下降成一条直线
	fig, ax = plt.subplots(figsize=(6,6))
	ax.plot(np.arange(iters), cost, 'r')
	ax.set_xlabel('Iters')
	ax.set_ylabel('Cost')
	ax.set_title('Error vs. Training Epoch')
	plt.show()
	return

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
		if(sysstr =="Windows"):
			file = open('datas/'+today+'-'+str(i)+'.txt', 'wb')
		else:
			file = open('/www/pyshell/datas/'+today+'-'+str(i)+'.txt', 'wb')
		
		file.write(html)
		file.close()
		data = json.loads(html)
		return data

#所有外部接口
def dataWork():
	urls = ['https://data.ripple.com/v2/exchanges/XRP/CNY+rKiCet8SdvWxPXnAgYarFUXMh1zCPz432Y?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/CNY+razqQKzJRdB4UxFPWf5NEpEG3WMkmwgcXA?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/CNY+rPT74sUcTBTQhkHVD54WGncoqXEAMYbmH7?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/USD+rvYAfWj5gh67oV6fW32ZzP3Aw4Eubs59B?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/XRP/USD+rhub8VRN55s94qWKDv6jmDy1pUykJzF3wq?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/ETH+rcA8X3TVMST1n3CJeAdGk1RdRCHii7N2h/XRP?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59','https://data.ripple.com/v2/exchanges/BTC+rchGBxcD1A1C2tdxF6papQYZ8kjRKMYcL/XRP?limit=1000&interval=5minute&start='+today+'T00:00:00&end='+today+'T23:59:59']
	i = 1
	for url in urls:
		jsonInterfaceRequest(url,i)
		time.sleep(1)
		i = i + 1
	with open(upPath, "a+") as h:
		h.write(localTime+"<br>\r")
		h.close()


#分类：假设模型
def sigmoid(z):
	return 1/(1+np.exp(-z))

#分类：多变量代价函数
def classCost(theta, X, y):
	theta = np.matrix(theta)
	X = np.matrix(X)
	y = np.matrix(y)
	first = np.multiply(-y, np.log(sigmoid(X * theta.T)))
	second = np.multiply((1 - y), np.log(1 - sigmoid(X * theta.T)))
	return np.sum(first - second) / (len(X))

#分类：计算梯度下降的步长
def gradient(theta, X, y):
	theta = np.matrix(theta)
	X = np.matrix(X)
	y = np.matrix(y)

	parameters = int(theta.ravel().shape[1])
	grad = np.zeros(parameters)

	error = sigmoid(X * theta.T) - y

	for i in range(parameters):
		term = np.multiply(error, X[:,i])
		grad[i] = np.sum(term) / len(X)

	return grad

#分类：预测下一步分类结果
def predict(theta, X):
	probability = sigmoid(X * theta.T)
	return [1 if x >= 0.5 else 0 for x in probability]

#分类：找到最优theta,分析分类成功率
def checkClassResult(x,z):
	theta = np.zeros(x.shape[1])
	result = opt.fmin_tnc(func=classCost, x0=theta, fprime=gradient, args=(x, z))
	theta_min = np.matrix(result[0])
	predictions = predict(theta_min, x)
	correct = [1 if ((a == 1 and b == 1) or (a == 0 and b == 0)) else 0 for (a, b) in zip(predictions, z)]
	accuracy = (sum(map(int, correct))/len(correct))

	return theta_min,accuracy

#分类：正则化计算梯度下降的步长
def gradientReg(theta, X, y):
	theta = np.matrix(theta)
	X = np.matrix(X)
	y = np.matrix(y)

	parameters = int(theta.ravel().shape[1])
	grad = np.zeros(parameters)

	error = sigmoid(X * theta.T) - y

	for i in range(parameters):
		term = np.multiply(error, X[:,i])

		if (i == 0):
			grad[i] = np.sum(term) / len(X)
		else:
			grad[i] = (np.sum(term) / len(X)) + ((learningRate / len(X)) * theta[:,i])

	return grad

#分类：正则化代价函数
def classCostReg(theta, X, y):
	theta = np.matrix(theta)
	X = np.matrix(X)
	y = np.matrix(y)
	first = np.multiply(-y, np.log(sigmoid(X * theta.T)))
	second = np.multiply((1 - y), np.log(1 - sigmoid(X * theta.T)))
	reg = (learningRate / 2 * len(X)) * np.sum(np.power(theta[:,1:theta.shape[1]], 2))
	return np.sum(first - second) / (len(X)) + reg

#分类：正则化找到最优theta,分析分类成功率
def checkClassResultReg(x,z):
	theta = np.zeros(x.shape[1])
	result = opt.fmin_tnc(func=classCostReg, x0=theta, fprime=gradientReg, args=(x, z))
	theta_min = np.matrix(result[0])
	predictions = predict(theta_min, x)
	correct = [1 if ((a == 1 and b == 1) or (a == 0 and b == 0)) else 0 for (a, b) in zip(predictions, z)]
	accuracy = (sum(map(int, correct))/len(correct))

	return theta_min,accuracy
	
if __name__ == "__main__":
	sysstr = platform.system()
	doTime = str(datetime.datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S'))
	localTime = str(datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
	today = str(datetime.datetime.utcnow().strftime('%Y-%m-%d'))  #str(today - datetime.timedelta(days))
	
	if(sysstr =="Windows"):
		upPath = today+'-work.html'
	else:
		upPath = '/www/web/intcpu/pro/'+today+'-work.html'
		
	#dataWork()
		
	learningRate = 1
	
	#这是分别预测最后两组数据
	datas = fileData([1,3,4,5,6,7,2])
	x,y,z,lastData,nowData,lastTime,lastPrice,nowTime,nowPrice = dataToMatrix(datas)
	
	#分类
	theta_min,accuracy = checkClassResult(x,z)
	lastClass = predict(theta_min, lastData)
	nowClass = predict(theta_min, nowData)
	
	#正则化分类
	theta_min_reg,accuracy_reg = checkClassResultReg(x,z)
	lastClass_reg = predict(theta_min_reg, lastData)
	nowClass_reg = predict(theta_min_reg, nowData)

	#使用正规方程
	nextPrice,willPrice = normalEqnResult(x,y,lastData,nowData)

	#使用梯度学习 - 不适合  因为变动值范围太小 随便调整学习率和迭代次数，结果变化都很大
	#nextPrice_des,willPrice_des = gradientDescentResult(x,y,lastData,nowData)


	#记录文件
	if(sysstr =="Windows"):
		dataPath = today+'.html'
	else:
		dataPath = '/www/web/intcpu/pro/'+today+'.html'
		
	strs = "\r<br>utc time:<span style='color:red'>"+doTime+"</span>   beijing time:<span style='color:red'>"+localTime+"</span><br>"
	strs = strs + "\r<br>"+lastTime+" : ["+lastPrice+"]->"+nextPrice+"  ==>>  "+nowTime+" : ["+nowPrice+"]->"+willPrice

	strs = strs + "\r<br>lastClass["+str(lastClass[0])+"] nowClass["+str(nowClass)+"] accuracy["+str(accuracy)+"] lastClass_reg["+str(lastClass_reg)+"] nowClass_reg["+str(nowClass_reg)+"] accuracy_reg["+str(accuracy_reg)+"]<br>"
	strs = strs.replace('T',' ')	
	strs = strs.replace('Z','')	
	
	with open(dataPath, "a+") as h:
		h.close()
	with open(dataPath, "r+") as f:
		 old = f.read()
		 f.seek(0)
		 f.write(strs)
		 f.write(old[134:])
		 f.close()
