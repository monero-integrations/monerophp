# Monero Library
A Monero library written in PHP by the Monero-Integrations team.

## How It Works
This library has 2 main parts, a Monero daemon JSON RPC API wrapper, `daemonRPC.php`, and a Monero/Cryptonote toolbox, `cryptonote.php` (with both lower level functions used in Monero related cryptograhy and higher level methods for things like generating Monero private/public keys.)

In addition to these features, there are other lower-level libraries included for portability, *eg.* an ed25519 library, a SHA3 library, *etc.*

## Preview
![Preview](https://user-images.githubusercontent.com/4107993/37871070-c2ab36a8-2f99-11e8-9860-bc208230e47e.png)

## Configuration
### Requirements
 - PC + internet
 - Ubuntu or Debian
 - Monero daemon
 - PHP server like XMPP, Apache or NGINX
    - cURL PHP extension for JSON RPC API(s)
    - GMP PHP extension for about 100x faster calculations (as opposed to BCMath)
 
###

Step 1: Start monerod on testnet
```bash
monerod --testnet --detach
```

Step 2: Edit `example.php` with your ip (`127.0.0.1` for localhost) and port of your monerod (in the example it's `127.0.0.1:28080`)

Step 3: Open your browser with the IP address of XMPP, apache or NGINX server (or just "localhost") and execute example.php.  If everything has been set up correctly, it will print information from your Monero daemon
