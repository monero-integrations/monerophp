# Monero Library
A Monero library written in PHP by the Monero-Integrations team.

## How It Works
This library has 3 main parts:

1. A Monero daemon JSON RPC API wrapper, `daemonRPC.php`
2. A Monero wallet (monero-wallet-rpc) JSON RPC API wrapper, `walletRPC.php`
3. A Monero/Cryptonote toolbox, `cryptonote.php`, with both lower level functions used in Monero related cryptograhy and higher level methods for things like generating Monero private/public keys.

In addition to these features, there are other lower-level libraries included for portability, *eg.* an ed25519 library, a SHA3 library, *etc.*

## Preview
![Preview](http://i.imgur.com/fyfRCOS.png)

## Configuration
### Requirements
 - PC + internet
 - Ubuntu or Debian
 - Monero daemon
 - PHP server like XMPP, Apache or NGINX
    - cURL PHP extension for JSON RPC API(s)
    - GMP PHP extension for about 100x faster calculations (as opposed to BCMath)
 
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
