<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'company_name',
        'salary_min',
        'salary_max',
        'is_remote',
        'job_type',
        'status',
        'published_at'
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function attributes() {
        return $this->belongsToMany(Attribute::class, 'job_attribute_values')->withPivot('value');
    }

    public function attributeValues()
    {
        return $this->hasMany(JobAttributeValue::class);
    }

    public function getEavValue(string $attributeName)
    {
        return $this->attributeValues()
            ->whereHas('attribute', fn($q) => $q->where('name', $attributeName))
            ->value('value');
    }

    public function setEavValue(string $attributeName, string $value): void
    {
        $attribute = Attribute::firstOrCreate(['name' => $attributeName]);
        $this->attributeValues()->updateOrCreate(
            ['attribute_id' => $attribute->id],
            ['value' => $value]
        );
    }
}