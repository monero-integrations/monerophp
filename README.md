# Monero Library
The Monero Library is built by SerHack with PHP and lots of coffee!

## How It Works
The Monero Library (aka Monero_Payments.php) will try to connect to your Monero RPC Daemon (monero-wallet-rpc). See the next section for how to use it and for more information).
Monero RPC Daemon has a json api that can communicate with the Monero Library. The Monero Library will automatically know your address and other things.

## Preview
![Preview](http://i.imgur.com/fyfRCOS.png)

## Configuration
### Requirements
 - PC + internet
 - Ubuntu or Debian
 - Monero daemon
 - PHP server like XMPP, Apache or NGINX
 
###

Step 1: Start the Monero Daemon as Testnet
```bash
monerod --testnet --detach
```

Step 2: Start the Monero Wallet RPC
```bash
monero-wallet-rpc --testnet --rpc-bind-port 28080 --disable-rpc-login --wallet-file /path/walletfile
```

Step 3: Edit example.php with your ip (`127.0.0.1` for localhost) and port of Monero Wallet RPC (in the example it's `127.0.0.1:28080`)


Step 4: Open your browser with your ip of XMPP, apache or NGINX server and execute example.php. If the library works, it will print your Monero address
