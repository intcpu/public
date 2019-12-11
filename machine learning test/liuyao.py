# -*- coding: UTF-8 -*-
import numpy as np

#六爻取数:
#1.用梯度下降法计算所得的预测结果数一般为5.94504762这种格式，在梯度下降中学习速率和次数分别为49和50的整倍数
#2.我们从小数点后面八位去掉第一次出现的最低数，去掉最后一次出现的最高数，剩余六位按照顺序以单数为阳爻，双数为阴爻的规则依次匹配初爻到六爻
#3.小数点后取得的六个数中如果与小数点前一位有相同之数，则此数匹配的爻为变爻,如果六个数中有0或9，则此数位亦为变爻

def qushu(shu):
	shu = str("%.8f" % shu)
	dian  = shu.find('.')
	yishu = shu[dian+1:dian+9]
	
	max_i = 0
	min_i = 0
	max   = yishu[0]
	min   = yishu[0]
	shu_len = len(yishu)
	for i in [0,1,2,3,4,5,6,7]:
		if i < shu_len:
			i_str = yishu[i]
		else:
			i_str = yishu[i-shu_len]
			yishu += i_str
		if i_str >= max:
			max_i = i
			max = i_str
		if i_str < min:
			min_i = i
			min = i_str
	if max_i < min_i: max_i,min_i = min_i,max_i
	yishu = yishu[0:min_i]+yishu[min_i+1:max_i]+yishu[max_i+1:]
	return yishu,shu[dian-1:dian]



def quyao(shu,shu_bian):
	yao = ''
	bian = ''
	for i in [0,1,2,3,4,5]:
		yao_i = str(int(shu[i])%2)
		yao += yao_i
		if shu[i] == shu_bian:
			bian += str((int(shu[i])+1)%2)
		else:
			if shu[i] == '0':
				bian += '1'
			elif shu[i] == '9':
				bian += '0'
			else:
				bian += yao_i
	return yao,bian