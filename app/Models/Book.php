<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'author',
        'isbn',
        'description',
        'cover_image',
        'total_stock',
        'available_stock',
        'published_year',
        'publisher',
        'pages',
        'language',
        'is_active',
    ];

    protected $casts = [
        'total_stock' => 'integer',
        'available_stock' => 'integer',
        'pages' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });

        // Clear cache when book is updated
        static::updated(function ($book) {
            Cache::forget("book:{$book->id}:availability");
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the category that owns the book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the reservations for the book.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get active reservations (pending or approved)
     */
    public function activeReservations(): HasMany
    {
        return $this->reservations()->whereIn('status', ['pending', 'approved', 'borrowed']);
    }

    /**
     * Get pending reservations ordered by queue position
     */
    public function queuedReservations(): HasMany
    {
        return $this->reservations()
            ->where('status', 'pending')
            ->whereNotNull('queue_position')
            ->orderBy('queue_position');
    }

    /**
     * Check if book is available
     */
    public function isAvailable(): bool
    {
        return $this->available_stock > 0;
    }

    /**
     * Get cached availability
     */
    public function getCachedAvailability(): int
    {
        return Cache::remember("book:{$this->id}:availability", 60, function () {
            return $this->available_stock;
        });
    }

    /**
     * Decrement stock and clear cache
     */
    public function decrementStock(): void
    {
        $this->decrement('available_stock');
        Cache::forget("book:{$this->id}:availability");
    }

    /**
     * Increment stock and clear cache
     */
    public function incrementStock(): void
    {
        if ($this->available_stock < $this->total_stock) {
            $this->increment('available_stock');
            Cache::forget("book:{$this->id}:availability");
        }
    }

    /**
     * Get next queue position for this book
     */
    public function getNextQueuePosition(): int
    {
        $maxPosition = $this->reservations()
            ->whereIn('status', ['pending'])
            ->max('queue_position');

        return ($maxPosition ?? 0) + 1;
    }

    /**
     * Scope for available books
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_stock', '>', 0);
    }

    /**
     * Scope for active books
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'ilike', "%{$search}%")
                ->orWhere('author', 'ilike', "%{$search}%")
                ->orWhere('isbn', 'ilike', "%{$search}%");
        });
    }

    /**
     * Get cover image URL
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image && file_exists(public_path('storage/' . $this->cover_image))) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/book-placeholder.png');
    }
}
