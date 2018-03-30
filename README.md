# Monero Library
A Monero library written in PHP by the [Monero Integrations](https://monerointegrations.com) [team](https://github.com/monero-integrations/monerophp/graphs/contributors).

## How It Works
This library has 3 main parts:

1. A Monero daemon JSON RPC API wrapper, `daemonRPC.php`
2. A Monero wallet (`monero-wallet-rpc`) JSON RPC API wrapper, `walletRPC.php`
3. A Monero/Cryptonote toolbox, `cryptonote.php`, with both lower level functions used in Monero related cryptograhy and higher level methods for things like generating Monero private/public keys.

In addition to these features, there are other lower-level libraries included for portability, *eg.* an ed25519 library, a SHA3 library, *etc.*

## Preview
![Preview](https://user-images.githubusercontent.com/4107993/38056594-b6cd6e14-3291-11e8-96e2-a771b0e9cee3.png)

## Configuration
### Requirements
 - Monero daemon
 - Webserver with PHP, for example XMPP, Apache, or NGINX
    - cURL PHP extension for JSON RPC API(s)
    - GMP PHP extension for about 100x faster calculations (as opposed to BCMath)

Debian (or Ubuntu) are recommended.
 
---

1. Start the Monero daemon (`monerod`) on testnet
```bash
monerod --testnet --detach
```

2. Start the Monero wallet RPC interface (`monero-wallet-rpc`) on testnet
```bash
monero-wallet-rpc --testnet --rpc-bind-port 28083 --disable-rpc-login --wallet-dir /path/to/wallet/directory
```

3. Edit `example.php` with your the IP address of `monerod` and `monero-wallet-rpc` (use `127.0.0.1:28081` and `127.0.0.1:28083`, respectively, for testnet)

4. Open your browser with your IP address of local webserver (*eg.* XMPP, Apache/Apache2, NGINX, *etc.*) and execute example.php.  If everything has been set up correctly, information from your Monero daemon and wallet will be displayed.
