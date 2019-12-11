# -*- coding: UTF-8 -*-
import math
import platform
import numpy as np;
import matplotlib.pyplot as plt

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

#正规方程预测结果
def normalEqnResult(x,y,x1):
	theta 	 = normalEqn(x,y)
	y1 = x1@theta
	return y1


#缩放特征，特征归一化，谨慎使用
def zoomFeature(data):
	data = (data - data.mean())/data.std()
	return data

#梯度下降预测结果
def gradientDescentResult(x,y,alpha,iters,x1):
	#x = zoomFeature(x)
	#alpha = 0.00000000001
	#iters = 6000
	theta = np.mat((np.zeros(x.shape[1]))) #初始化全部为0向量
	theta, cost = gradientDescent(x, y, theta, alpha, iters)
	gradientDescentPrint(iters,cost)
	y1 = x1@theta.T
	return y1

#梯度下降数据分布观察
def gradientDescentPrint(iters,cost):
	if(platform.system() !="Windows"):
		return
	#不断的调试 alpha 与 iters 使迭代次数iters与误差cost图案逐渐下降成一条直线
	fig, ax = plt.subplots(figsize=(6,6))
	ax.plot(np.arange(iters), cost, 'r')
	ax.set_xlabel('Iters')
	ax.set_ylabel('Cost')
	ax.set_title('Error vs. Training Epoch')
	plt.show()
	return