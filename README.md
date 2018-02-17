# Monero Library
A Monero library written in PHP by the Monero-Integrations team.

## How It Works
This library has 2 parts. The first is a wrapper for the monero-wallet-rpc JSON RPC API. (Monero_Payments.php)
The second is a Monero/Cryptonote toolbox (cryptonote.php) with both lower level functions used in Monero related cryptograhy and higher level methods for things like generating Monero private/public keys.

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
