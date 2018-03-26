# Monero Library
A Monero library written in PHP by the Monero-Integrations team.

## How It Works
This library has 2 main parts, a Monero daemon JSON RPC API wrapper, `daemonRPC.php`, and a Monero/Cryptonote toolbox, `cryptonote.php` (with both lower level functions used in Monero related cryptograhy and higher level methods for things like generating Monero private/public keys.)

In addition to these features, there are other lower-level libraries included for portability, *eg.* an ed25519 library, a SHA3 library, *etc.*

## Preview
![Preview](https://user-images.githubusercontent.com/4107993/37871070-c2ab36a8-2f99-11e8-9860-bc208230e47e.png)

## Configuration
### Requirements
 - Monero daemon
 - Webserver with PHP, for example XMPP, Apache, or NGINX
    - cURL PHP extension for JSON RPC API(s)
    - GMP PHP extension for about 100x faster calculations (as opposed to BCMath)

Debian (or Ubuntu) are recommended.
 
###

Step 1: Start monerod on testnet

```bash
monerod --testnet --detach
```

Step 2: Start monero-wallet-rpc
```bash
monero-wallet-rpc --testnet --rpc-bind-port 28080 --disable-rpc-login --wallet-file /path/walletfile
```

Step 3: Edit example.php with your the IP address of monerod and monero-wallet-rpc (use `127.0.0.1:28081` and `127.0.0.1:28080`, respectively, for testnet)

Step 4: Open your browser with your IP address of local webserver (*eg.* XMPP, Apache/Apache2, NGINX, *etc.*) and execute example.php.  If everything has been set up correctly, information from your Monero daemon and wallet will be displayed

