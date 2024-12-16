<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends BaseModel implements JWTSubject, AuthenticatableContract
{
    use Authenticatable, Authorizable, HasFactory, Notifiable;

    const LUGGIN = 1;

    protected $assets = ["profile"];
    // protected $assets = ["identification_doc", "profile"];

    protected $appends = ["is_verified", "is_active", "unverified" ,"country"];

    protected $toVerified;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password',
        'email',
        "last_name",
        "pseudo",
        "birthdate",
        "first_name",
        "phone",
        "address",
        "place_residence",
        "country_id",
        "identification_type_id",
        "identification_doc",
        "role_id",
        "about_me",
        "profile"
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    private $verifications;


    /**
     * Get the user's preferred language.
     *
     * @return string
     */
    public function getPreferredLanguageAttribute(): string
    {
        return $this->attributes['preferred_language'] ?? config('app.fallback_locale', 'en');
    }

    /**
     * Set the user's preferred language.
     *
     * @param string $value
     */
    public function setPreferredLanguageAttribute(string $value): void
    {
        $this->attributes['preferred_language'] = $value;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getIsVerifiedAttribute()
    {
        return !array_key_exists("identification_doc", $this->getUnverifiedAttribute()) or Role::USER > $this->role_id;
    }

    public function getCountryAttribute()
    {
        return Country::where('id', $this->country_id)->first();
    }

    public function getUnverifiedAttribute()
    {
        $toVerify = [];

        if ($this->getVerifications()->where("update_value", $this->email)->where("verified_at", null)->count() > 0 or $this->getVerifications()->where("type", Verification::EMAIL)->count() == 0) {
            $toVerify["email"] = $this->email;
        }
        if ($this->getVerifications()->where("update_value", $this->phone)->where("verified_at", null)->count() > 0 or $this->getVerifications()->where("type", Verification::PHONE)->count() == 0) {
            $toVerify["phone"] = $this->phone;
        }
        if ($this->getVerifications()->where("update_value", $this->attributes["identification_doc"])->where("verified_at", null)->count() > 0 or $this->getVerifications()->where("type", Verification::DOC)->count() == 0) {
            $toVerify["identification_doc"] = $this->identification_doc;
        }
        return $toVerify;
    }

    /**
     * @return mixed
     */
    public function getVerifications(): Builder
    {
        return Verification::where("user_id", $this->id);
    }

    public function verifications(){
        return $this->hasMany("App\Models\Verification");
    }

    public function getIsActiveAttribute()
    {
        return !array_key_exists("email", $this->getUnverifiedAttribute()) or !array_key_exists("phone", $this->getUnverifiedAttribute()) or Role::USER > $this->role_id;
    }

    public function routeNotificationForSns($notification)
    {
        //Add plus
        return "+".str_replace("+","",$this->toVerified);
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getToVerified()
    {
        return $this->toVerified;
    }

    /**
     * @param mixed $toVerified
     */
    public function setToVerified($toVerified): void
    {
        $this->toVerified = $toVerified;
    }

    /**
     * Get user connected devices
     * @return HasMany
     */
    public function devices()
    {
        return $this->hasMany("App\Models\Fcm");
    }

    /**
     * Get user account settings
     * @return HasOne
     */
    public function accountSetting()
    {
        return $this->hasOne("App\Models\AccountSetting");
    }
}
