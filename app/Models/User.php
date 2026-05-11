<?php

namespace App\Models;

use App\Enums\DatabaseNameEnums;
use App\Helpers\SettingHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SilkPanel\SilkroadModels\Models\Account\AbstractTbUser;
use SilkPanel\SilkroadModels\Models\Account\SkSilk;
use SilkPanel\SilkroadModels\Models\Account\SkSilkBuyList;
use SilkPanel\SilkroadModels\Models\Portal\AphChangedSilk;
use SilkPanel\SilkroadModels\Models\Portal\MuUser;
use App\Models\WebmallPurchase;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @property int $jid
 * @property string $silkroad_id
 * @property string $reflink
 * @property int|null $referrer_id
 * @property string $register_ip
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReflink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRegisterIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSilkroadId($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    // if you want dont want to use email verification, just remove the "implements MustVerifyEmail" and the "use Illuminate\Contracts\Auth\MustVerifyEmail;" line at the top of this file.

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = DatabaseNameEnums::MYSQL->value;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'jid',
        'pjid',
        'silkroad_id',
        'reflink',
        'referrer_id',
        'register_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user has verified their email address.
     * Respects the email_verification_required setting.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        // If email verification is disabled in settings, always return true
        if (! SettingHelper::get('email_verification_required', true)) {
            return true;
        }

        // Otherwise, check the actual verification status
        return ! is_null($this->email_verified_at);
    }

    #region functions

    /**
     * Set the game password for the user. This will update the password in the game database as well.
     *
     * @param string $password
     * @param integer $jid
     * @param AbstractTbUser $tbUser
     * @param MuUser $muUser
     * @return void
     */
    public function setGamePassword(string $password, int $jid, AbstractTbUser $tbUser, MuUser $muUser): void
    {
        if ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser) {
            $tbUser->where('JID', $jid)->update(['password' => md5($password)]);
        } elseif ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\ISRO\TbUser) {
            $muUser->where('JID', $jid)->update(['UserPwd' => md5($password)]);
            $tbUser->where('PortalJID', $jid)->update(['password' => md5($password)]);
        }
    }

    #endregion functions


    #region relation

    /**
     * Get the tbuser associated with the User.
     *
     * @param AbstractTbUser $tbUser
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    private function getTbUser(AbstractTbUser $tbUser)
    {
        if ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser) {
            return $this->belongsTo(\SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser::class, 'jid', 'JID');
        } else {
            return $this->belongsTo(\SilkPanel\SilkroadModels\Models\Account\ISRO\TbUser::class, 'pjid', 'PortalJID');
        }
    }

    /**
     * Get the tbuser associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tbuser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->getTbUser(resolve(AbstractTbUser::class));
    }

    /**
     * Get the muUser associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function muuser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MuUser::class, 'pjid', 'JID');
    }

    /**
     * Get the shard associated with the silkroad user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shardUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        $charModel = $this->tbuser()->getRelated()->getShardUserModelClass();

        return $this->belongsToMany($charModel, '_User', 'UserJID', 'CharID', 'jid', 'CharID');
    }

    /**
     * Get the SkSilk record associated with the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getSkSilk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SkSilk::class, 'jid', 'JID');
    }

    /**
     * Get the SkSilkBuyList records associated with the user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getSkSilkHistory(): \Illuminate\Database\Eloquent\Relations\HasManyThrough|\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return match (config('silkpanel.version')) {
            'isro' => $this->hasManyThrough(AphChangedSilk::class, MuUser::class, 'JID', 'JID', 'pjid', 'JID'),
            default => $this->hasMany(SkSilkBuyList::class, 'UserJID', 'jid'),
        };
    }

    /**
     * Webmall purchases made by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webmallPurchases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WebmallPurchase::class, 'user_id');
    }

    /**
     * Referrals this user has given out (users they invited).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * The referral record for this user (who invited them).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function referral(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    #endregion relation
}
