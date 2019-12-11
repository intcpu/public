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

#缩放特征，特征归一化，谨慎使用
def zoomFeature(data):
	data = (data - data.mean())/data.std()
	return data

#梯度下降预测结果
def gradientDescentResult(x,y,nowData):
	#x = zoomFeature(x)

	#学习率越小越能找到相应下降梯度
	alpha = 0.000000000000000000001
	iters = 5000
	theta = np.mat((np.zeros(x.shape[1]))) #初始化全部为0向量
	g, cost = gradientDescent(x, y, theta, alpha, iters)
	gradientDescentPrint(iters,cost)
	willPrice = nowData@g.T
	return str(willPrice)

#梯度下降数据分布观察
def gradientDescentPrint(iters,cost):
	if(SYS_NAME !="Windows"):
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

#学习率
LEARNIN_GRATE = 1
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
			grad[i] = (np.sum(term) / len(X)) + ((LEARNIN_GRATE / len(X)) * theta[:,i])

	return grad

#分类：正则化代价函数
def classCostReg(theta, X, y):
	theta = np.matrix(theta)
	X = np.matrix(X)
	y = np.matrix(y)
	first = np.multiply(-y, np.log(sigmoid(X * theta.T)))
	second = np.multiply((1 - y), np.log(1 - sigmoid(X * theta.T)))
	reg = (LEARNIN_GRATE / 2 * len(X)) * np.sum(np.power(theta[:,1:theta.shape[1]], 2))
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

#回归：theta正规方程求解
def normalEqn(X,y):
	theta = np.linalg.inv(X.T@X)@X.T@y
	return theta

#正规方程预测结果
def normalEqnResult(x,y,nowData):
	theta 	 = normalEqn(x,y)
	print(theta)
	willPrice = nowData@theta
	return str(willPrice)


SYS_NAME = platform.system()

#最小浮动  手续费0.00075*2
MIN_FLOAT = 0.002
#基于最小浮动的统计行
MIN_ROW = 0
#最大最小价开单调整价
DIFF_PRICE = 3
#多单开仓价
MIN_PRICE = 0
#空单开仓价
MAX_PRICE = 0
#设置固定行率
FIXED_FLOAT = 0.9
#过去稳定最高价平均价
AVG_PRICE_MAX = 0
#过去稳定最低价平均价
AVG_PRICE_MIN = 0
#过去稳定收盘平均价
AVG_PRICE_CLOSE = 0
#最后最大价
LAST_PRICE_MAX = 0
#最后最小价
LAST_PRICE_MIN = 0
#最后收盘价价
LAST_PRICE_CLOSE = 0
#持仓量变化率
LAST_VOL = 0
#最高价变化率
LAST_MAX = 0
#收盘价变化率
LAST_CLOSE = 0

LINUX_PUT_PATH = '/www/web/intcpu/pro/'
DATA_DIR_DAY = 'data/day/'
DATA_DIR_HOUR = 'data/hour/'
DATA_DIR_MIN = 'data/min/'
DATA_DIR_RESULT = 'data/result/'

#设置统计行,开仓价,平仓价
def setMinRow(datas):
	global MIN_ROW,DIFF_PRICE,MIN_PRICE,MAX_PRICE
	min = max = 0 
	i = len(datas) - 1
	while 1:
		MIN_ROW+=1
		min = min if(min < datas[i,2] and min != 0) else datas[i,2]
		max = max if(max > datas[i,1]) else datas[i,1]
		if (max - min - DIFF_PRICE) >= datas[i,3]*MIN_FLOAT:
			MAX_PRICE = max-1
			MIN_PRICE = min+1
			break;
		i-=1

#设置过去稳定交易的平均仓位波动
#last_rate 如果等于1  就是和平均波动相等 
#avg_rate 是平均波动
def openVolAvgFloat(datas):
	global AVG_PRICE_MAX,AVG_PRICE_MIN,AVG_PRICE_CLOSE,LAST_VOL,LAST_MAX,LAST_CLOSE
	MAX = 9
	m = len(datas)
	n = len(datas[0])
	i = 0
	all_vol = 0
	all_max = 0
	all_close = 0

	rate_max = [0]*MAX
	rate_close = [0]*MAX
	rate_vol = [0]*MAX
	rise_max = [0]*MAX
	rise_close = [0]*MAX
	rise_vol = [0]*MAX
	avg_price_max = 0
	avg_price_min = 0
	avg_price_close = 0

	while (i < MAX):
		rate_vol[i] = datas[m-i-1,n-1]-datas[m-i-2,n-1]
		rate_max[i] = datas[m-i-1,1]-datas[m-i-2,1]
		rate_close[i] = datas[m-i-1,3]-datas[m-i-2,3]
		if (i>1):
			inx = i -1
			rise_vol[inx] = 1 if(rate_vol[inx] == 0) else abs(rate_vol[i]/rate_vol[inx])
			rise_max[inx] = 1 if(rate_max[inx] == 0) else abs(rate_max[i]/rate_max[inx])
			rise_close[inx] = 1 if(rate_close[inx] == 0) else abs(rate_close[i]/rate_close[inx])
			if (i < MAX -1):
				all_vol += rise_vol[inx]
				all_max += rise_max[inx]
				all_close += rise_close[inx]

				avg_price_max += datas[m-i-2,1]
				avg_price_min += datas[m-i-2,2]
				avg_price_close += datas[m-i-2,3]
			else:
				AVG_PRICE_MAX = avg_price_max/(inx-1)
				AVG_PRICE_MIN = avg_price_min/(inx-1)
				AVG_PRICE_CLOSE = avg_price_close/(inx-1)

				avg_vol  = all_vol/inx
				LAST_VOL = rise_vol[inx]/avg_vol
				avg_max  = all_max/inx
				LAST_MAX = rise_max[inx]/avg_max
				avg_close= all_close/inx
				LAST_CLOSE = rise_close[inx]/avg_close
		i +=1

#设置基础矩阵数据
def setMatrix(datas):
	global MIN_ROW
	fixed_row = round(MIN_ROW*FIXED_FLOAT)
	float_row = MIN_ROW - fixed_row
	max_len   = len(datas)
	f = len(datas)-MIN_ROW+1
	l = len(datas[0])
	kd = l*(float_row+1)
	x = np.mat(np.zeros((f,kd)))
	y = np.mat(np.zeros((f,1)))
	z = np.mat(np.zeros((f,1)))
	i = 0
	while i != max_len - fixed_row:
		j = 0
		x[i,0] = datas[i,0]
		while j != fixed_row:
			x[i,1] = x[i,1] if(x[i,1] > datas[i+j,1]) else datas[i+j,1]
			x[i,2] = x[i,2] if(x[i,2] < datas[i+j,2] and x[i,2] != 0) else datas[i+j,2]
			x[i,3] = datas[i+j,3]
			x[i,4] += datas[i+j,4]
			x[i,5] += datas[i+j,5]
			x[i,6] = (datas[i+j,6]+x[i,6])/2
			x[i,7] = (datas[i+j,7]+x[i,7])/2
			x[i,8] += datas[i+j,8]
			x[i,9] += datas[i+j,9]
			x[i,10] = (datas[i+j,10]+x[i,10])/2
			x[i,11] = (datas[i+j,11]+x[i,11])/2
			x[i,12] = datas[i+j,12]
			j+=1
		k = 0
		while k != float_row:
			m = 0
			while m != l:
				x[i,(l+m+k)] = datas[(fixed_row+i),(m+k)]
				m+=1
			k+=1
		y[i,:] = datas[i+fixed_row,3]
		z[i,:] = 1 if datas[i+fixed_row,3] > datas[i+fixed_row-1,3] else 0
		nowData = x[i,0:]
		i+=1
	return x,y,z,nowData
	

def setResult():
	global LAST_PRICE_MAX,LAST_PRICE_MIN,LAST_PRICE_CLOSE
	START_TIME = datetime.datetime.now()
	TODAY = str(START_TIME.strftime('%Y-%m-%d'))
	YESTERDAY = str((START_TIME-datetime.timedelta(days=1)).strftime('%Y-%m-%d'))
	NOW_TIME = str(START_TIME.strftime('%H:%M:%S'))


	datas_last = np.loadtxt(DATA_DIR_MIN+YESTERDAY+'-XBTUSD.csv',delimiter=',', skiprows=1,usecols=(2,3,4,5,24,25,26,27,31,32,33,34,36))
	if(os.path.exists(DATA_DIR_MIN+TODAY+'-XBTUSD.csv')):
		datas_now = np.loadtxt(DATA_DIR_MIN+TODAY+'-XBTUSD.csv',delimiter=',', skiprows=1,usecols=(2,3,4,5,24,25,26,27,31,32,33,34,36))
		datas = np.vstack((datas_last,datas_now))
	else:
		datas = datas_last

	#setMinRow(datas)

	openVolAvgFloat(datas)

	m = len(datas)

	LAST_PRICE_MAX = datas[m-1,1]
	LAST_PRICE_MIN = datas[m-1,2]
	LAST_PRICE_CLOSE = datas[m-1,3]

	
	# x,y,z,nowData = setMatrix(datas)

	# # #使用正规方程
	# willPrice = normalEqnResult(x,y,nowData)
	# print(willPrice)
	
	#分类
	# theta_min,accuracy = checkClassResult(x,z)
	# nowClass = predict(theta_min, nowData)

	# #正则化分类
	# theta_min_reg,accuracy_reg = checkClassResultReg(x,z)
	# nowClass_reg = predict(theta_min_reg, nowData)

	#使用梯度学习 - 不适合  因为真实变动值范围太小 但是梯度下降随便调整学习率和迭代次数，结果变化都很大
	#willPrice_des = gradientDescentResult(x,y,nowData)
	#print(willPrice_des)
	#