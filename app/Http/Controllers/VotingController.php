<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use SilkPanel\Voting\Models\VotingSite;

class VotingController extends Controller
{
    public function index(Request $request): View
    {
        $votingSites = VotingSite::active()
            ->orderBy('sort_order')
            ->get();

        $user = $request->user();
        $jid = config('silkpanel.version') === 'isro' ? $user->jid : $user->jid;

        $sites = $votingSites->map(function (VotingSite $site) use ($user, $jid) {
            $site->url = str_replace('{JID}', $jid, $site->url);

            return [
                'site' => $site,
                'can_vote' => $site->canUserVote($user),
                'next_vote' => $site->getNextVoteTime($user),
            ];
        });

        return view('template::voting.index', compact('sites'));
    }
}
