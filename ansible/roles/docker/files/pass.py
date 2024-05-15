#!/usr/bin/env python  
# -*- coding: utf-8 -*-  
import string  
import random  
import sys  
import itertools  
  
a = q = None  
  
if sys.version_info.major == 2:  
    try:  
        input = raw_input  
        # print(sys.version)  
    except NameError:
        pass  
  
  
def get_random(l=12, a='n'):  
    s = string.ascii_letters + string.digits  
    if a == 'y':  
        s = s + string.punctuation  
    while True:  
        r = random.sample(s, l)  
        yield ''.join(r)  
  
try:  
    if len(sys.argv) >= 3:  
        l = int(sys.argv[1])  
        a = str(sys.argv[2])  
        q = int(sys.argv[3])  
    else:  
        l = int(input('Enter the lenght: '))  
        a = str(input('Generate with punctuation? [y/n]: '))  
        q = int(input('Passphrases quantity: '))  
        assert 8 < l <= 20, 'Entered wrong number'  
        assert a in ['y', 'n'], 'Wrong answer for punctuation'  
        assert 0 < q <= 10, 'Entered wrong number'  
except:  
    l = 12  
    q = 1  
    # print('Wrong input digit, making with default length... %d letters and digits.' % (l))  
finally:  
    if not a:  
        a = 'n'  
    get_random(l, a)  
    r = get_random(l, a)  
    for p in itertools.islice(r, q):  
        print(p)
