<?php

/**
 * Attempt to assert the validity of an email address
 *
 * @param string $email
 * @return boolean
 */
function validEmail($email)
{
    // Check the formatting is correct
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }

    // Let's skip the DNS check when testing
    if (app()->environment('testing')) {
        return true;
    }

    // Check for the existence MX records for this domain
    $split = explode('@', $email);
    $domain = array_pop($split);
    return !empty(dns_get_record($domain, DNS_MX));
}
