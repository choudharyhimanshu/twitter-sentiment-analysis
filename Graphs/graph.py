# Himanshu Choudhary (12298)
import pymongo as pm
import time
import datetime
import sys
import numpy as np
import matplotlib.pyplot as plt

def sec2hour(seconds):
	m, s = divmod(seconds, 60)
	h, m = divmod(m, 60)
	return h

# ==================== TEST =======================

# secs = np.arange(2100,3600,120)

# for time in secs:
# 	print time
# 	m, s = divmod(time, 60)
# 	h, m = divmod(m, 60)
# 	temp =  "%02d:%02d:%02d" % (h, m, s)
# 	print temp

# sys.exit()
# =================================================
client = pm.MongoClient()
db = client.tweetsdata
collection = db.indiantweets

# print datetime.timedelta(0,86340)

secs = np.arange(0,3600*24,300)

steps = len(secs)

print steps

# sys.exit()

count_tot = 0
count_posi = 0
count_neg = 0
count_neut = 0

arr_total = np.zeros(steps)
arr_posi = np.zeros(steps)
arr_neg = np.zeros(steps)
arr_neut = np.zeros(steps)

# steps = 100

for i in range(steps-1):
	# print secs[i]
	m, s = divmod(secs[i], 60)
	h, m = divmod(m, 60)
	curr_time =  "%02d:%02d:%02d" % (h, m, s)
	# curr_time = datetime.timedelta(0,secs[i])
	# hour = curr_time.hours
	# print(curr_time)
	t0 = "Tue Apr 14 "+str(curr_time)+" +0000 2015"
	# print t0
	m, s = divmod(secs[i+1], 60)
	h, m = divmod(m, 60)
	curr_time =  "%02d:%02d:%02d" % (h, m, s)
	# curr_time = datetime.timedelta(0,secs[i+1])
	tf = "Tue Apr 14 "+str(curr_time)+" +0000 2015"
	print tf
	result = collection.find({"$and" : [{"created_at" : {"$gte": t0}},{"created_at": {"$lt": tf}}]})
	# .sort("created_at",1)
	# print result
	# print curr_time
	for val in result:
		count_tot += 1
		arr_total[i] += 1
		if (val['mood'] == "Positive"):
			count_posi += 1
			arr_posi[i] += 1
		elif (val['mood'] == "Negative"):
			count_neg += 1
			arr_neg[i] += 1
		else :
			count_neut += 1
			arr_neut[i] += 1

steps -= 1
plt.plot(secs[0:steps],arr_total[0:steps],'o',color='k',label='$Total \ Tweets$')
plt.plot(secs[0:steps],arr_posi[0:steps],'o',color='g',label="$Positive \ Tweets$")
plt.plot(secs[0:steps],arr_neg[0:steps],'o',color='r',label="$Negative \ Tweets$")
plt.plot(secs[0:steps],arr_neut[0:steps],'o',color='b',label="$Neutral \ Tweets$")
plt.xlabel("Time (in seconds)")
plt.ylabel("Number of Tweets")
plt.legend()
plt.show()