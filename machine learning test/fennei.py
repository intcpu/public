# -*- coding: UTF-8 -*-
import math
import numpy as np;
import scipy.optimize as opt

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