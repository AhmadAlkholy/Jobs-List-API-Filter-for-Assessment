# Job Listing API

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

A robust Laravel API for job listings with advanced filtering capabilities across standard fields, relationships, and dynamic attributes.

## Table of Contents
- [Features](#features)
- [API Documentation](#api-documentation)
- [Filtering Syntax](#filtering-syntax)
- [Examples](#examples)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [License](#license)

## Features

- **Comprehensive Job Model** with all required fields
- **Many-to-Many Relationships**:
  - Languages
  - Locations 
  - Categories
- **EAV (Entity-Attribute-Value) System** for dynamic attributes
- **Advanced Filtering API** with support for:
  - Standard field filtering
  - Relationship filtering
  - EAV attribute filtering
  - Logical operators (AND/OR) and grouping
- **Optimized Query Building** with JobFilterService

## API Documentation

### GET `/api/jobs`

Returns paginated job listings with filtering capabilities.

**Parameters:**
| Parameter | Type     | Description |
|-----------|----------|-------------|
| `filter`  | string   | Filter expression (see syntax below) |
| `page`    | integer  | Page number (default: 1) |


**Example Response:**
```json
{
    "message": "Jobs fetched successfully.",
    "data": [
        {
            "id": 6,
            "title": "Hoist and Winch Operator",
            "description": "Quae voluptatem id laborum qui dolor itaque similique cupiditate.",
            "salary_min": "75925.00",
            "salary_max": "174113.00",
            "is_remote": false,
            "job_type": "part-time",
            "status": "draft",
            "published_at": null,
            "created_at": "2025-03-29T08:37:47.000000Z",
            "languages": [
                {
                    "id": 2,
                    "name": "Java"
                },
                {
                    "id": 5,
                    "name": "Ruby"
                }
            ],
            "categories": [
                {
                    "id": 2,
                    "name": "Mobile Development"
                }
            ],
            "locations": [
                {
                    "id": 4,
                    "city": "Kingberg",
                    "state": "Oregon",
                    "country": "Nauru"
                }
            ],
            "attributes": [
                {
                    "id": 58,
                    "name": "mollitia",
                    "type": "date",
                    "options": null,
                    "value": "2023-06-29"
                }
            ]
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 1,
        "last_page": 1
    },
    "errors": []
}
```

## Filtering Syntax

The API supports advanced filtering through a expressive query parameter syntax. All filters are passed via the `filter` query parameter.

### Basic Field Filtering

#### Text Fields (`title`, `description`, `company_name`)
- Equality: `field=value`  
  Example: `title=Developer`
- Inequality: `field!=value`  
  Example: `company_name!=Acme`
- Contains: `field LIKE value`  
  Example: `description LIKE remote`

#### Numeric Fields (`salary_min`, `salary_max`)
- Equality: `field=value`  
  Example: `salary_min=50000`
- Comparisons: `field>value`, `field>=value`, `field<value`, `field<=value`  
  Example: `salary_max<=100000`

#### Boolean Fields (`is_remote`)
- Equality: `field=1|0`  
  Example: `is_remote=1`

#### Enum Fields (`job_type`, `status`)
- Equality: `field=value`  
  Example: `job_type=full-time`
- Multiple values: `field IN (value1,value2)`  
  Example: `status IN (published,archived)`

#### Date Fields (`published_at`, `created_at`)
- Equality: `field=YYYY-MM-DD`  
  Example: `published_at=2023-01-01`
- Comparisons: `field>YYYY-MM-DD`, etc.  
  Example: `created_at>=2023-01-01`

### Relationship Filtering

#### Languages
- Has any of: `languages HAS_ANY (value1,value2)`  
  Example: `languages HAS_ANY (PHP,JavaScript)`
- Existence: `languages EXISTS`  
  (Returns jobs with any languages)

#### Locations
- Is any of: `locations IS_ANY (value1,value2)`  
  Example: `locations IS_ANY (New York,Remote)`
  
#### Categories
- Is any of: `categories IS_ANY (value1,value2)`  
  Example: `categories IS_ANY (Software Development,Project Management)`

### EAV Attribute Filtering

Prefix attribute names with `attribute:`:

- Text: `attribute:field=value`  
  Example: `attribute:certification=required`
- Number: `attribute:field>value`  
  Example: `attribute:years_experience>=5`
- Boolean: `attribute:field=1|0`  
  Example: `attribute:security_clearance=1`
- Select: `attribute:field IN (value1,value2)`  
  Example: `attribute:education_level IN (bachelor,master)`

### Logical Operators

- AND: `condition1 AND condition2`  
  Example: `salary_min>50000 AND is_remote=1`
- OR: `condition1 OR condition2`  
  Example: `job_type=contract OR job_type=freelance`
- Grouping: `(condition1 OR condition2) AND condition3`  
  Example: `(job_type=full-time OR salary_min>80000) AND locations IS_ANY (Remote)`

## Examples

### Simple Filters
1. Remote jobs:  
   `/api/jobs?filter=is_remote=1`

2. Full-time jobs in NY:  
   `/api/jobs?filter=job_type=full-time AND locations IS_ANY (New York)`

### Intermediate Filters
3. PHP or Python jobs paying $80k+:  
   `/api/jobs?filter=(languages HAS_ANY (PHP,Python)) AND salary_min>=80000`

4. Jobs requiring 5+ years experience:  
   `/api/jobs?filter=attribute:years_experience>=5`

### Complex Filters
5. Senior remote developer positions:  
    `/api/jobs?filter=
(job_type=full-time AND salary_min>=90000)
AND (languages HAS_ANY (JavaScript,TypeScript))
AND is_remote=1
AND attribute:level=senior`

6. Urgent contract jobs in tech hubs:  
`/api/jobs?filter=
(job_type=contract AND attribute:is_urgent=1)
AND (locations IS_ANY (San Francisco,New York,London))
AND categories=Technology`

## 🛠️ Installation

### Requirements
- PHP 8.1+
- Composer 2.0+
- MySQL 8.0+ / PostgreSQL 13+
- Redis (for caching)


<br/>

### Setup

1\. Clone the repository:
```bash
git clone https://github.com/AhmadAlkholy/Jobs-List-API-Filter-for-Assessment
```

<br/>

2\. Install dependencies:
```bash
composer install
```

<br/>

3\. Configure environment:
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
Make sure to add your database credentials in .env file.

<br/>

4\. Set up database:
```bash
php artisan migrate:fresh --seed
```
The seeded data might look weird specially the attribute names check the generate database before starting to test the api.

<br/>

5\. Start development server:
```bash
php artisan serve
```
<br/>

## 📜 License

MIT License. See [LICENSE](https://opensource.org/license/mit) for details.