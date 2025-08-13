<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar', // New field for user avatar
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

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($user){
            $deviceCode = 'PLTS-'. strtoupper(substr(md5($user->id.time()), 0, 8));

            $device = Device::create([
                'user_id'=> $user->id,
                'device_code' => $deviceCode,
                'status' => 'offline',
                'last_seen' => null
            ]);

            DeviceSettings::create([
                'device_id' => $device->id,
                'temp1_threshold' => 0.00,
                'temp2_threshold' => 0.00,
                'alert_enabled' => true,
                'email_notification' => true,
                'data_collection_interval' => 5, // in minutes
            ]);
            SensorData::create([
                'device_id' => $device->id,
                'battery_a' => 0.00,
                'battery_b' => 0.00,
                'battery_c' => 0.00,
                'battery_d' => 0.00,
                'temperature_1' => 0.00,
                'temperature_2' => 0.00,
                'pln_volt' => 0.00,
                'pln_current' => 0.00,
                'pln_watt' => 0.00,
                'relay_1' => false,
                'relay_2' => false,
            ]);
        });
    }
    public function device()
    {
        return $this->hasOne(Device::class);
    }
}
