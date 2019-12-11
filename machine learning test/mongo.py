# -*- coding: UTF-8 -*-
from pymongo import MongoClient


#链接mongon
def mongondb(host = 'localhost',port = 27017):
	client = MongoClient(host, port)
	db = client.ripple  #读取ripple库
	daylog = db.daylog  #读取daylog集合

	# #单条插入
	#daylog.insert({"name":"zhangsan","age":18})
	# #或
	# daylog.save({"name":"zhangsan","age":18})
	
	# #多条插入
	# users=[{"name":"zhangsan","age":18},{"name":"lisi","age":20}]  
	# daylog.insert(users)

	# #查询全部
	#for i in daylog.find({"open_time":{"$regex":"2018-01-01"}}).sort([("open_time",1)]):
	#	print(i)

	# #查询name=zhangsan的
	# for i in daylog.find({"name":"zhangsan"}):
	# 	print(i)

	# #例：查询集合中age大于25的所有记录
	# for i in daylog.find({"age":{"$gt":25}}):
	# 	print(i)

	# #下面表示跳过两条数据后读取6条
	# for i in daylog.find().skip(2).limit(6):
	#     print(i)

	# #找出age是20、30、35的数据
	# for i in daylog.find({"age":{"$in":(20,30,35)}}):
	#     print(i)

	# #找出age是20或35的记录
	# for i in daylog.find({"$or":[{"age":20},{"age":35}]}):
	#     print(i)

	db.daylog.remove()
	
	return client

mongondb()