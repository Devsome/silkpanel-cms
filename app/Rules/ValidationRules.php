<?php

namespace App\Rules;

trait ValidationRules
{
    /**
     * @return string[]
     */
    protected function usernameRules(): array
    {
        return ['required', 'string', 'alpha_num', 'lowercase', 'min:6', 'max:16', 'unique:users'];
    }

    /**
     * @return string[]
     */
    protected function referralRules(): array
    {
        return ['sometimes', 'nullable', 'exists:users,reflink'];
    }
}
