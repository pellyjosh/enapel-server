<?php

namespace App\Services\DisasterRecovery;

class NetworkAddressService
{
    public function privateIpv4Addresses(): array
    {
        $addresses = [];

        if (function_exists('net_get_interfaces')) {
            foreach (net_get_interfaces() ?: [] as $interface) {
                foreach (($interface['unicast'] ?? []) as $unicast) {
                    $address = $unicast['address'] ?? null;

                    if ($this->isPrivateIpv4($address)) {
                        $addresses[] = $address;
                    }
                }
            }
        }

        foreach (gethostbynamel(gethostname()) ?: [] as $address) {
            if ($this->isPrivateIpv4($address)) {
                $addresses[] = $address;
            }
        }

        $addresses = array_values(array_unique($addresses));
        sort($addresses);

        return $addresses;
    }

    protected function isPrivateIpv4(?string $address): bool
    {
        if (! is_string($address) || ! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        if ($address === '127.0.0.1') {
            return false;
        }

        $long = ip2long($address);

        if ($long === false) {
            return false;
        }

        return ($long >= ip2long('10.0.0.0') && $long <= ip2long('10.255.255.255'))
            || ($long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255'))
            || ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255'));
    }
}
