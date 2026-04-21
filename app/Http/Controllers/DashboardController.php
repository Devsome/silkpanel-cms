<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $silkData = $this->getSilkData($user);
        $characters = $this->getCharacters($user);
        $votingEnabled = class_exists('SilkPanel\Voting\Models\VotingSite');
        $votingData = $votingEnabled ? $this->getVotingData($user) : null;
        $referralEnabled = (bool) Setting::get('referral_enabled', false);
        $referralData = $referralEnabled ? $this->getReferralData($user) : null;

        return view('template::dashboard', compact(
            'silkData',
            'characters',
            'votingEnabled',
            'votingData',
            'referralEnabled',
            'referralData'
        ));
    }

    private function getSilkData($user): array
    {
        $isIsro = config('silkpanel.version') === 'isro';

        if ($isIsro) {
            try {
                $jcash = $user->muuser?->JCash;

                return [
                    'type' => 'isro',
                    'silk' => (int) ($jcash?->Silk ?? 0),
                    'premium_silk' => (int) ($jcash?->PremiumSilk ?? 0),
                    'total' => (int) ($jcash?->Silk ?? 0) + (int) ($jcash?->PremiumSilk ?? 0),
                ];
            } catch (\Throwable $e) {
                return ['type' => 'isro', 'silk' => 0, 'premium_silk' => 0, 'total' => 0];
            }
        }

        try {
            $skSilk = $user->getSkSilk;

            return [
                'type' => 'vsro',
                'silk_own' => (int) ($skSilk?->silk_own ?? 0),
                'silk_gift' => (int) ($skSilk?->silk_gift ?? 0),
                'silk_point' => (int) ($skSilk?->silk_point ?? 0),
                'total' => (int) ($skSilk?->silk_own ?? 0)
                    + (int) ($skSilk?->silk_gift ?? 0)
                    + (int) ($skSilk?->silk_point ?? 0),
            ];
        } catch (\Throwable $e) {
            return ['type' => 'vsro', 'silk_own' => 0, 'silk_gift' => 0, 'silk_point' => 0, 'total' => 0];
        }
    }

    private function getCharacters($user): \Illuminate\Support\Collection
    {
        try {
            return $user->shardUsers()
                ->get()
                ->filter(fn($char) => $char->CharID != 0 && $char->CharName16 !== 'dummy')
                ->sortByDesc('CurLevel')
                ->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function getVotingData($user): array
    {
        $votingSiteClass = 'SilkPanel\Voting\Models\VotingSite';
        $votingLogClass = 'SilkPanel\Voting\Models\VotingLog';

        $canVote = false;
        $lastVote = null;

        try {
            /** @var \SilkPanel\Voting\Models\VotingSite $site */
            $sites = $votingSiteClass::active()->get();
            $canVote = $sites->contains(fn($site) => $site->canUserVote($user));
        } catch (\Throwable $e) {
            // Voting DB unavailable – silently degrade
        }

        try {
            $lastVote = $votingLogClass::where('user_id', $user->id)
                ->where('callback_received', true)
                ->latest('voted_at')
                ->first();
        } catch (\Throwable $e) {
            //
        }

        return [
            'can_vote' => $canVote,
            'last_vote' => $lastVote,
            'voted_today' => $lastVote && $lastVote->voted_at?->isToday(),
        ];
    }

    private function getReferralData($user): array
    {
        try {
            $referrals = $user->referrals()->with(['referred.shardUsers'])->get();

            $validCount = $referrals->where('status', 'valid')->count();
            $pendingCount = $referrals->where('status', 'pending')->count();
            $totalSilkEarned = $referrals->sum('silk_rewarded');

            $mapped = $referrals->sortByDesc('created_at')->map(function ($referral) {
                $topChar = $referral->referred?->shardUsers
                    ?->filter(fn($c) => $c->CharID != 0 && $c->CharName16 !== 'dummy')
                    ->sortByDesc('CurLevel')
                    ->first();

                $referral->character_name = $topChar?->CharName16 ?? null;

                return $referral;
            })->values();

            return [
                'reflink' => $user->reflink,
                'valid_count' => $validCount,
                'pending_count' => $pendingCount,
                'total_silk_earned' => $totalSilkEarned,
                'referrals' => $mapped,
            ];
        } catch (\Throwable $e) {
            return [
                'reflink' => $user->reflink,
                'valid_count' => 0,
                'pending_count' => 0,
                'total_silk_earned' => 0,
                'referrals' => collect(),
            ];
        }
    }
}
