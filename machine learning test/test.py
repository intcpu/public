import tkinter as tk
from tkinter import ttk
import matplotlib.pyplot as plt
import pandas as pd
import numpy as np
import talib as ta


ohlc_dict = {
	'open':'first',
	'high':'max',
	'low':'min',
	'close': 'last',
	'volume': 'sum'
}
datas = pd.read_csv('xbt.csv',index_col=0,usecols=(0,1,2,3,4,5),names=('date','close','open','high','low','volume'))

#datas.index = pd.DatetimeIndex(datas.date)
#data_15= datas.resample('15T', how=ohlc_dict, closed='left', label='left')

for x in range(0,len(datas['high'])):
	print(x)

# high = np.array(datas['high'])
# low = np.array(datas['low'])
# print(np.max(high))
# print(np.min(low))
# print(np.where(high == np.max(high)))
# print(np.where(low == np.min(low)))

# max_arr = ta.MAX(high,len(low))
# min_arr = ta.MIN(low,len(low))
# #real = ta.MININDEX(low,90)
# print(max_arr)
# print(min_arr)