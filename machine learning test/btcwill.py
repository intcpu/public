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
 
#json接口请求
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
	kd = len(datas[0])*3
	x = np.mat(np.zeros((l,kd))) 
	y = np.mat(np.zeros((l,1)))
	z = np.mat(np.zeros((l,1)))
	i = 2
	for val in datas:
		if(i-1 > l):
			break
		ti = 0
		for t in datas[i-2]:
			x[i-2,ti] = t
			ti = ti+1
		for t in datas[i-1]:
			x[i-2,ti] = t
			ti = ti+1
		for t in datas[i]:
			x[i-2,ti] = t
			ti = ti+1
		y[i-2,:] = datas[i][3]
		z[i-2,:] = 1 if datas[i][3] > val[0] else 0
		nowData = x[i-2,0:]
		i = i+1
	return x,y,z,nowData

#正规方程预测结果
def normalEqnResult(x,y,nowData):
	theta 	 = normalEqn(x,y)
	willPrice = nowData@theta
	return str(willPrice)


#缩放特征，特征归一化，谨慎使用
def zoomFeature(data):
	data = (data - data.mean())/data.std()
	return data

#梯度下降预测结果
def gradientDescentResult(x,y,nowData):
	#x = zoomFeature(x)

	alpha = 0.00000000001
	iters = 6000
	theta = np.mat((np.zeros(x.shape[1]))) #初始化全部为0向量
	g, cost = gradientDescent(x, y, theta, alpha, iters)
	gradientDescentPrint(iters,cost)
	willPrice = nowData@g.T
	return str(willPrice)

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

	learningRate = 1

	datas = np.loadtxt('2018-07-05-XBTUSD.csv',delimiter=',', skiprows=1,usecols=(2,3,4,5,6,7,8,9,10,11,16,17,18,19,26,27,33,34))

	x,y,z,nowData = dataToMatrix(datas)
	print(nowData)
	#分类
	theta_min,accuracy = checkClassResult(x,z)
	nowClass = predict(theta_min, nowData)

	#正则化分类
	theta_min_reg,accuracy_reg = checkClassResultReg(x,z)
	nowClass_reg = predict(theta_min_reg, nowData)

	#使用正规方程
	willPrice = normalEqnResult(x,y,nowData)

	#使用梯度学习 - 不适合  因为变动值范围太小 随便调整学习率和迭代次数，结果变化都很大
	#nextPrice_des,willPrice_des = gradientDescentResult(x,y,nowData)


	#记录文件
	if(sysstr =="Windows"):
		dataPath = today+'.html'
	else:
		dataPath = '/www/web/intcpu/pro/'+today+'.html'
		
	strs = "\rbeijing time:<span style='color:red'>"+localTime+"</span><br>"
	strs = strs + "\r["+str(nowClass)+"]->"+str(nowClass_reg)+" ==== ["+str(willPrice)+"]"

	
	with open(dataPath, "a+") as h:
		h.close()
	with open(dataPath, "r+") as f:
		 old = f.read()
		 f.seek(0)
		 f.write(strs)
		 f.write(old[134:])
		 f.close()
