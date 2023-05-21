from machine import Pin, PWM
import network
import urequests
import utime
import ujson
import random

# ! Note:
# * Le site doit avoir un compte nommé "raspberrypi" pour que le code fonctionne
# * Vérifiez que la base de données est bien mise à jour avec le fichier iot.sql fourni 

# ! Schematics:
# LED RGB
#  - R: Pin 13
#  - G: Pin 14
#  - B: Pin 15
#  - GND: GND
# BOUTON
#  - Pin 12

# =================================
# ==>  INIT
# =================================

# WLAN Config
wlan = network.WLAN(network.STA_IF)
wlan.active(True)

# SSID & PASSWORD
ssid = 'IIM_Private'
password = 'Creatvive_Lab_2023'

# ? URL de l'API, à replacer avec votre IP (celle sur le réseau local)
url = 'https://iimersive.kan-a-pesh.ml/api/iot'

wlan.connect(ssid, password)

# LEDS
r_pin = PWM(Pin(13, mode=Pin.OUT))
g_pin = PWM(Pin(14, mode=Pin.OUT))
b_pin = PWM(Pin(15, mode=Pin.OUT))
r_pin.freq(1_000)
g_pin.freq(1_000)
b_pin.freq(1_000)

# BUTTON
button_pin = Pin(12, Pin.IN, Pin.PULL_UP)

# Wait for connection
while not wlan.isconnected():
    utime.sleep(1)
    pass

print("SYS: Connected!")

# =================================
# ==>  POKAPI
# =================================
color_dict = [
    [255, 100, 20],
    [97, 84, 66],
    [118, 121, 122],
    [25, 191, 69],
    [171, 111, 189],
    [57, 186, 237],
    [87, 50, 5],
    [75, 75, 75],
    [255, 255, 255],
    [247, 247, 47],
    [197, 153, 195]
]

def post_tweet():
    r = urequests.post(url)
    r.close()
    print("Posted tweet")

def get_last_tweet():
    r = urequests.get(url)
    r_json = r.json()
    r.close()
    return r_json

def parse_type_color(tweet):
    tag = tweet['payload']['tag']
    print("Found tag: " + tag)
    
    color = color_dict[tag]
    
    # Map 0-255 to 0-65535
    color = [int(unit * 65535 / 255) for unit in color]

    return color

def set_led_color(color):
    r_pin.duty_u16(color[0])
    g_pin.duty_u16(color[1])
    b_pin.duty_u16(color[2])

while(True):
    try:
        # Check button
        if button_pin.value() == 0:
            post_tweet()

        # Check tag
        tweet = get_last_tweet()
        color = parse_type_color(tweet)
        set_led_color(color)
        utime.sleep(1)
    except Exception as e:
        print(e)