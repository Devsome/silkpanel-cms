<?php

namespace App\Helpers;

use SilkPanel\WidgetsDashboard\Helper\VerifyHelper;

class LicenseHelper
{
    /**
     * Determine whether the current SilkPanel license is active and valid.
     *
     * The underlying verification result is cached by VerifyHelper, so this
     * is safe to call frequently (e.g. from Filament navigation/visibility).
     */
    public static function isValid(): bool
    {
        $key = config('silkpanel.api_key');

        if (! is_string($key) || $key === '') {
            return false;
        }

        return (bool) VerifyHelper::verifyLicenseKey($key);
    }
}
