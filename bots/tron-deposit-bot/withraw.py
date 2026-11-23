# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from tronpy import Tron
from tronpy.keys import PrivateKey

# client = Tron(network='nile')
# priv_key = PrivateKey(bytes.fromhex("2661f052f6dc026e73c0184c4aa175916edca86d42ad72e394f90fdbad80aed1"))

# txn = (
#     client.trx.transfer("TU55om4TvkZRnYEQSzK6civpQuEyBzSwkU", "TCFed4iYkAFhPcvx1rmL4Sq8Xir4SfTdxR", 1_000)
#     .memo("test memo")
#     .build()
#     .inspect()
#     .sign(priv_key)
#     .broadcast()
# )
# print(txn)

# integers representing half & one Tron
HALF_TRON = 500000
ONE_TRON = 1000000 #equals 1 TRX

# your wallet information
WALLET_ADDRESS = "TU55om4TvkZRnYEQSzK6civpQuEyBzSwkU" #MY WALLET
PRIVATE_KEY = "2661f052f6dc026e73c0184c4aa175916edca86d42ad72e394f90fdbad80aed1"
priv_key = PrivateKey(bytes.fromhex("2661f052f6dc026e73c0184c4aa175916edca86d42ad72e394f90fdbad80aed1"))
client = Tron(network='nile')

def send_tron(amount, wallet):
    try:
        
        # create transaction and broadcast it
        txn = (
            client.trx.transfer(WALLET_ADDRESS, str(wallet), 25_000_000)
            .memo("test")
            .with_owner(WALLET_ADDRESS)
            .build()
            .inspect()
            .sign(priv_key)
            .broadcast()
        )
        return txn.wait()

    # return the exception
    except Exception as ex:
        return ex