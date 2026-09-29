<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;

        protected $fillable = [
        'user_id',
        'nomor_permintaan',
        'tanggal',
        'catatan',
        'status',
        'diproses_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PermintaanItem::class);
    }

    /**
     * Generate unique nomor permintaan, contoh: REQ-2026-08-001
     */
    public static function generateNomorPermintaan(): string
    {
        $prefix = 'REQ-' . date('Y-m') . '-';
        $last = self::where('nomor_permintaan', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->nomor_permintaan, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }
}
