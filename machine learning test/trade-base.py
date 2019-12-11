import hashlib
import hmac
import json
import time
import os
import urllib
import pandas as pd
import numpy as np

from websocket import create_connection

UTC_FORMAT = "%Y-%m-%dT%H:%M:%S.%fZ"
symbol = "XBTUSD"
symbol_file = "XBTUSD-test"
min_unit = 60
hour_unit = 3600
day_unit = 86400
min_lists = {}
hour_lists = {}
day_lists = {}
all_open_btc = 0
all_open_vol = 0
buy_order_vol  = 0
sell_order_vol = 0
buy_order_avg_price   = 0
sell_order_avg_price  = 0

###
# websocket-apikey-auth-test.py
#
# Reference Python implementation for authorizing with websocket.
# See https://www.bitmex.com/app/wsAPI for more details, including a list
# of methods.
###

# These are not real keys - replace them with your keys.
API_KEY = "RaMt7m3s9RouUfJ-gTI-fQul"
API_SECRET = "PFh6zjScTtAv7a25pZxbPr_LkYgvepfZWJKp8vm581OkFtzh"

#{"op": "subscribe", "args": [<SubscriptionTopic>]}

# Switch these comments to use testnet instead.
# BITMEX_URL = "wss://testnet.bitmex.com"
BITMEX_URL = "wss://www.bitmex.com"

VERB = "GET"
ENDPOINT = "/realtime"


def main():
    """Authenticate with the BitMEX API & request account information."""
    test_with_message()
    #test_with_querystring()
def test_with_message():
    global min_lists
    global hour_lists
    global day_lists
    global buy_order_vol
    global sell_order_vol
    global buy_order_avg_price
    global sell_order_avg_price
    global all_open_btc
    global all_open_vol
    # This is up to you, most use microtime but you may have your own scheme so long as it's increasing
    # and doesn't repeat.
    nonce = int(round(time.time() * 1000))
    # See signature generation reference at https://www.bitmex.com/app/apiKeys
    signature = bitmex_signature(API_SECRET, VERB, ENDPOINT, nonce)

    # Initial connection - BitMEX sends a welcome message.
    ws = create_connection(BITMEX_URL + ENDPOINT)
    print("Receiving Welcome Message...")
    result = ws.recv()
    print(result)

    # Send API Key with signed message.
    request = {"op": "authKey", "args": [API_KEY, nonce, signature]}
    ws.send(json.dumps(request))
    print("Sent Auth request")
    result = ws.recv()
    print(result)

    # Send a request that requires authorization.
    request = {"op": "subscribe", "args": ["trade:"+symbol,"instrument:"+symbol,"orderBook10:"+symbol]}
    ws.send(json.dumps(request))
    print("Sent subscribe")
        
    while 1:
        result = ws.recv()
        result = json.loads(result)
        table = result.get('table')

        data = False
        i_data = False
        o_data = False

        #交易信息
        if table and table == "trade":
            data = result.get('data')
        #行情信息
        if table and table == "instrument":
            i_data = result.get('data')
        #委托信息
        if table and table == "orderBook10":
            o_data = result.get('data')
            buy_order_vol  = 0
            sell_order_vol = 0
            buy_order_avg_price   = 0
            sell_order_avg_price  = 0
            for i,val in o_data[0]['bids']:
                buy_order_vol += val
                buy_order_avg_price += i*val
            buy_order_avg_price /=buy_order_vol
            for i,val in o_data[0]['asks']:
                sell_order_vol += val
                sell_order_avg_price += i*val
            sell_order_avg_price /=sell_order_vol
        #未平仓价值
        if i_data and i_data[0].get("openInterest"):
            all_open_vol = i_data[0].get("openInterest")
        #未平仓BTC数
        if i_data and i_data[0].get("openValue"):
            all_open_btc = i_data[0].get("openValue")
            all_open_btc = all_open_btc/100000000
        #处理交易
        if data:
            for order_s in data :
                #交易时间戳
                timeArray = time.strptime(order_s['timestamp'], UTC_FORMAT)
                cur_time = int(time.mktime(timeArray))
                
                #交易整点
                open_min_s = cur_time - ( cur_time % min_unit )
                open_hour_s = cur_time - ( cur_time % hour_unit )
                open_day_s = cur_time - ( cur_time % day_unit )

                
                date_s = time.strftime("%Y-%m-%d %H:%M:00", timeArray)
                month_s = time.strftime("%Y-%m-%d %H:00:00", timeArray)
                day_s = time.strftime("%Y-%m-%d", timeArray)
                
                date_file_name = '/www/shell/bm-zuihuode/data/min/'+time.strftime("%Y-%m-%d")+"-"+symbol_file+".csv"
                month_file_name = '/www/shell/bm-zuihuode/data/hour/'+time.strftime("%Y-%m")+"-"+symbol_file+".csv"
                day_file_name = '/www/shell/bm-zuihuode/data/day/'+time.strftime("%Y")+"-"+symbol_file+".csv"
                
                insert_data(order_s,open_min_s,date_s,date_file_name,1)
                insert_data(order_s,open_hour_s,month_s,month_file_name,2)
                insert_data(order_s,open_day_s,day_s,day_file_name,3)

    ws.close()

def insert_data(order,open_min,date,file_name,type):
    global min_lists
    global hour_lists
    global day_lists
    global buy_order_vol
    global sell_order_vol
    global buy_order_avg_price
    global sell_order_avg_price
    global all_open_btc
    global all_open_vol

    buy_vol  = order['size'] if (order['side'] == 'Buy') else 0
    sell_vol = 0 if (order['side'] == 'Buy') else order['size']
    all_vol  = buy_vol + sell_vol

    buy_btc  = order['homeNotional'] if (order['side'] == 'Buy') else 0
    sell_btc = 0 if (order['side'] == 'Buy') else order['homeNotional']
    all_btc  = buy_btc + sell_btc

    buy_price = order['price'] if (order['side'] == 'Buy') else 0
    sell_price = 0 if (order['side'] == 'Buy') else order['price']

    if type == 1:
        lists = min_lists
    elif type == 2:
        lists = hour_lists
    else:
        lists = day_lists

    if(open_min in lists.keys()):
        lists[open_min]['order_num'][0] += 1

        if order['price'] > lists[open_min]['max_price'][0]:
            lists[open_min]['max_price'][0] = order['price']
        if order['price'] < lists[open_min]['min_price'][0]:
            lists[open_min]['min_price'][0] = order['price']

        lists[open_min]['close_price'][0]   =  order['price']

        lists[open_min]['buy_btc'][0] += buy_btc
        lists[open_min]['sell_btc'][0] += sell_btc
        lists[open_min]['all_btc'][0] += all_btc

        if buy_price > lists[open_min]['buy_max_price'][0]:
            lists[open_min]['buy_max_price'][0] = buy_price
            lists[open_min]['buy_max_price_vol'][0]   = buy_vol
            
        if  buy_price > 0 and (buy_price < lists[open_min]['buy_min_price'][0] or lists[open_min]['buy_min_price'][0] == 0):
            lists[open_min]['buy_min_price'][0] = buy_price
            lists[open_min]['buy_min_price_vol'][0]   = buy_vol

        if sell_price > lists[open_min]['sell_max_price'][0]:
            lists[open_min]['sell_max_price'][0]   = sell_price
            lists[open_min]['sell_max_price_vol'][0]  = sell_vol

        if sell_price > 0 and (sell_price < lists[open_min]['sell_min_price'][0] or lists[open_min]['sell_min_price'][0] == 0):
            lists[open_min]['sell_min_price'][0]   = sell_price
            lists[open_min]['sell_min_price_vol'][0]  = sell_vol

        if buy_vol > lists[open_min]['buy_max_vol'][0]:
            lists[open_min]['buy_max_vol'][0]   = buy_vol
            lists[open_min]['buy_max_vol_price'][0]   = buy_price

        if buy_vol > 0 and (buy_vol < lists[open_min]['buy_min_vol'][0] or lists[open_min]['buy_min_vol'][0] == 0):
            lists[open_min]['buy_min_vol'][0]   = buy_vol
            lists[open_min]['buy_min_vol_price'][0] = buy_price

        if sell_vol > lists[open_min]['sell_max_vol'][0]:
            lists[open_min]['sell_max_vol'][0] = sell_vol
            lists[open_min]['sell_max_vol_price'][0]  = sell_price

        if sell_vol > 0 and (sell_vol < lists[open_min]['sell_min_vol'][0] or lists[open_min]['sell_min_vol'][0] == 0):
            lists[open_min]['sell_min_vol'][0] = sell_vol
            lists[open_min]['sell_min_vol_price'][0] = sell_price

        lists[open_min]['buy_vol'][0] += buy_vol
        lists[open_min]['sell_vol'][0] += sell_vol
        lists[open_min]['all_vol'][0] += all_vol


        lists[open_min]['vol_avg_price'][0]   = ((order['price']*order['size']) + (lists[open_min]['vol_avg_price'][0]*lists[open_min]['all_vol'][0]))/(all_vol+lists[open_min]['all_vol'][0])

        lists[open_min]['order_avg_price'][0] = (order['price']+lists[open_min]['order_avg_price'][0])/2

        lists[open_min]['order_avg_vol'][0]  = lists[open_min]['all_vol'][0]/lists[open_min]['order_num'][0]

    else:

        lists[open_min] = {}
        lists[open_min]['date']            = [date]
        lists[open_min]['all_btc']         = [all_btc]
        lists[open_min]['open_price']      = [order['price']]
        lists[open_min]['max_price']       = [order['price']]
        lists[open_min]['min_price']       = [order['price']]
        lists[open_min]['close_price']     = [order['price']]

        lists[open_min]['buy_btc']  =  [buy_btc]
        lists[open_min]['sell_btc'] =  [sell_btc]
        
        lists[open_min]['buy_max_price']       = [buy_price]
        lists[open_min]['buy_min_price']       = [buy_price]

        lists[open_min]['sell_max_price']      = [sell_price]
        lists[open_min]['sell_min_price']      = [sell_price]

        lists[open_min]['buy_max_vol']         = [buy_vol]
        lists[open_min]['buy_min_vol']         = [buy_vol]

        lists[open_min]['sell_max_vol']        = [sell_vol]
        lists[open_min]['sell_min_vol']        = [sell_vol]

        lists[open_min]['buy_max_vol_price']   = [buy_price]
        lists[open_min]['buy_min_vol_price']   = [buy_price]

        lists[open_min]['sell_max_vol_price']  = [sell_price]
        lists[open_min]['sell_min_vol_price']  = [sell_price]

        lists[open_min]['buy_max_price_vol']   = [buy_vol]
        lists[open_min]['buy_min_price_vol']   = [buy_vol]

        lists[open_min]['sell_max_price_vol']  = [sell_vol]
        lists[open_min]['sell_min_price_vol']  = [sell_vol]

        lists[open_min]['buy_vol']  =  [buy_vol]
        lists[open_min]['sell_vol'] =  [sell_vol]

        lists[open_min]['vol_avg_price']    = [order['price']]

        lists[open_min]['order_avg_price']  = [order['price']]

        lists[open_min]['order_avg_vol']    = [order['size']]
        lists[open_min]['all_vol']          = [all_vol]
        lists[open_min]['order_num']        = [1]

    lists[open_min]['buy_order_vol']    =  [buy_order_vol]
    lists[open_min]['sell_order_vol']   =  [sell_order_vol]
    lists[open_min]['buy_order_avg_price']      =  [buy_order_avg_price]
    lists[open_min]['sell_order_avg_price']     =  [sell_order_avg_price]

    lists[open_min]['all_open_btc']  =  [all_open_btc]
    lists[open_min]['all_open_vol']  =  [all_open_vol]

    all_keys = list(lists.keys())
    for key in all_keys:
        if key < open_min:
            if os.path.isfile(file_name) == False:
                init_data = {'date': [], 'all_btc': [], 'open_price': [], 'max_price': [], 'min_price': [], 'close_price': [], 'buy_btc': [], 'sell_btc': [], 'buy_max_price': [], 'buy_min_price': [], 'sell_max_price': [], 'sell_min_price': [], 'buy_max_vol': [], 'buy_min_vol': [], 'sell_max_vol': [], 'sell_min_vol': [], 'buy_max_vol_price': [], 'buy_min_vol_price': [], 'sell_max_vol_price': [], 'sell_min_vol_price': [], 'buy_max_price_vol': [], 'buy_min_price_vol': [], 'sell_max_price_vol': [], 'sell_min_price_vol': [], 'buy_vol': [], 'sell_vol': [], 'vol_avg_price': [], 'order_avg_price': [], 'order_avg_vol': [], 'all_vol': [], 'order_num': [], 'buy_order_vol': [], 'sell_order_vol': [], 'buy_order_avg_price': [], 'sell_order_avg_price': [], 'all_open_btc': [], 'all_open_vol': []}
                init_df = pd.DataFrame(init_data,index=init_data['date'])
                init_df.to_csv(file_name,float_format="%0.8f",sep=',', header=True,index=False)
            old = pd.read_csv(file_name)
            add = pd.DataFrame(lists[key],index=lists[key]['date'])
            new = old.append(add,sort=False)
            new.to_csv(file_name,float_format="%0.8f",sep=',', header=True,index=False)
            del(lists[key])
            
            if type == 1:
                min_lists = lists
            elif type == 2:
                hour_lists = lists
            else:
                day_lists = lists

def test_with_querystring():
    # This is up to you, most use microtime but you may have your own scheme so long as it's increasing
    # and doesn't repeat.
    nonce = int(round(time.time() * 1000))
    # See signature generation reference at https://www.bitmex.com/app/apiKeys
    signature = bitmex_signature(API_SECRET, VERB, ENDPOINT, nonce)

    # Initial connection - BitMEX sends a welcome message.
    ws = create_connection(BITMEX_URL + ENDPOINT +
                           "?api-nonce=%s&api-signature=%s&api-key=%s" % (nonce, signature, API_KEY))
    print("Receiving Welcome Message...")
    result = ws.recv()
    print("Received '%s'" % result)

    # Send a request that requires authorization.
    request = {"op": "subscribe", "args": "position"}
    ws.send(json.dumps(request))
    print("Sent subscribe")
    result = ws.recv()
    print("Received '%s'" % result)
    result = ws.recv()
    print("Received '%s'" % result)

    ws.close()


# Generates an API signature.
# A signature is HMAC_SHA256(secret, verb + path + nonce + data), hex encoded.
# Verb must be uppercased, url is relative, nonce must be an increasing 64-bit integer
# and the data, if present, must be JSON without whitespace between keys.
def bitmex_signature(apiSecret, verb, url, nonce, postdict=None):
    """Given an API Secret key and data, create a BitMEX-compatible signature."""
    data = ''
    if postdict:
        # separators remove spaces from json
        # BitMEX expects signatures from JSON built without spaces
        data = json.dumps(postdict, separators=(',', ':'))
    parsedURL = urllib.parse.urlparse(url)
    path = parsedURL.path
    if parsedURL.query:
        path = path + '?' + parsedURL.query
    # print("Computing HMAC: %s" % verb + path + str(nonce) + data)
    message = (verb + path + str(nonce) + data).encode('utf-8')
    print("Signing: %s" % str(message))

    signature = hmac.new(apiSecret.encode('utf-8'), message, digestmod=hashlib.sha256).hexdigest()
    print("Signature: %s" % signature)
    return signature

if __name__ == "__main__":
    main()