<?php

namespace App\Services;

use App\Helpers\SilkHelper;
use App\Models\Referral;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Create a pending referral record when a new user registers with a referral code.
     */
    public function createReferral(User $referrer, User $referred): Referral
    {
        return Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referred->id,
            'status' => 'pending',
            'silk_rewarded' => 0,
        ]);
    }

    /**
     * Process all pending referrals: validate based on character level and award silk.
     */
    public function processPendingReferrals(): int
    {
        if (! (bool) Setting::get('referral_enabled', false)) {
            return 0;
        }

        $minLevel = (int) Setting::get('referral_min_level', 20);
        $silkReward = (int) Setting::get('referral_silk_reward', 5);
        $silkType = (string) Setting::get('referral_silk_type', 'silk_own');

        $processed = 0;

        Referral::with(['referred.shardUsers', 'referrer'])
            ->where('status', 'pending')
            ->chunkById(50, function ($referrals) use ($minLevel, $silkReward, $silkType, &$processed) {
                foreach ($referrals as $referral) {
                    if ($this->hasReachedMinLevel($referral->referred, $minLevel)) {
                        $this->validateAndReward($referral, $silkReward, $silkType);
                        $processed++;
                    }
                }
            });

        return $processed;
    }

    /**
     * Check if the referred user's highest character level meets the minimum requirement.
     */
    private function hasReachedMinLevel(User $referred, int $minLevel): bool
    {
        try {
            $maxLevel = $referred->shardUsers()
                ->max('CurLevel');

            return (int) $maxLevel >= $minLevel;
        } catch (\Throwable $e) {
            Log::warning('ReferralService: Failed to fetch character level for user #' . $referred->id, [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark a referral as valid and award silk to the referrer.
     */
    private function validateAndReward(Referral $referral, int $silkReward, string $silkType): void
    {
        try {
            $jid = match (config('silkpanel.version')) {
                'isro' => $referral->referrer->pjid,
                default => $referral->referrer->jid,
            };

            SilkHelper::addSilk($jid, $silkReward, $silkType);

            $referral->update([
                'status' => 'valid',
                'silk_rewarded' => $silkReward,
                'rewarded_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ReferralService: Failed to reward referrer #' . $referral->referrer_id, [
                'referral_id' => $referral->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
