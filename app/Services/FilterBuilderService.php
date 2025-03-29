<?php

namespace App\Services;

use App\Models\Attribute;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FilterBuilderService
{
    public function applyFilters(Builder $query, string $filterExpression): void
    {
        $filterExpression = preg_replace("/\s+/", " ", strtolower($filterExpression));

        $filters = $this->splitByLogicalOps($filterExpression);
        $this->buildQuery($query, $filters);
    }

    private function splitByLogicalOps($str)
    {
        if (str_contains($str, ' or ') || str_contains($str, ' and ')) {
            $dividedByOrExpressions = $this->splitBy($str, ' or ');

            if (count($dividedByOrExpressions) > 1) {
                $ops = [];

                foreach ($dividedByOrExpressions as $dividedByOrExpression) {
                    $dividedByAndExpressions = $this->splitBy($dividedByOrExpression, ' and ');
                    if (count($dividedByAndExpressions) > 1) {
                        $ands = [];
                        foreach ($dividedByAndExpressions as $dividedByAndExpression) {
                            $dividedByAndExpression = $this->removeParentheses($dividedByAndExpression);
                            $ands['and'][] = $this->splitByLogicalOps($dividedByAndExpression);
                        }
                        $ops['or'][] = $ands;
                    }
                    else {
                        $ops['or'][] = $dividedByOrExpression;
                    }
                }

                return $ops;
            }
        }

        return $this->removeParentheses($str);
    }

    private function buildQuery(Builder $query, $filters): void
    {
        if (is_string($filters)) {
            $this->applyCondition($query, $filters);
        }
        else {
            foreach ($filters as $key => $subFilter) {
                if ($key == 'or') {
                    foreach($subFilter as $orFilter) {
                        $query->orWhere(function($subQuery) use($orFilter) {
                            $this->applySubQuery($subQuery, $orFilter);
                        });
                    }
                }
                elseif ($key == 'and') {
                    foreach($subFilter as $andFilter) {
                        $query->where(function($subQuery) use($andFilter) {
                            $this->applySubQuery($subQuery, $andFilter);
                        });
                    }
                }
            }
        }
    }

    private function applySubQuery($subQuery, $filter): void
    {
        if (is_string($filter)) {
            if (str_contains($filter, 'attribute:')) {
                $filter = substr($filter, 10);
                $subQuery->whereHas('attributes', function($q) use($filter) {
                    $expression = $this->extractExpressionParts($filter, '/^([^!=<>\s]+)\s*(=|!=|<=|>=|<|>|like)\s*(.+)$/i');
                    
                    $attribute = Attribute::where('name', $expression['left'])->first();

                    $q->where('name', $expression['left']);

                    if (!$attribute) {
                        return;
                    }

                    if ($this->isNumericOperator($expression['operator']) && $attribute->type == 'number') {
                        $q->where( DB::raw('CAST(`value` AS SIGNED)'), $expression['operator'], $expression['right']);
                    }
                    else {
                        $q->where('value', $expression['operator'], $expression['right']);
                    }
                });
            }
            else {
                $this->applyCondition($subQuery, $filter);
            }
        }
        else {
            $this->buildQuery($subQuery, $filter);
        }
    }

    private function applyCondition($query, $condition): void
    {
        if (preg_match('[ is_any | has_any | exists ]', $condition)) {
            $expression = $this->extractExpressionParts($condition, '/^(.+?)\s+(has_any|is_any)\s+(.+)$/i');

            $query->whereHas($expression['left'], function ($q) use($expression) {
                $inValues = explode(',', $this->removeParentheses($expression['right']));
                if ($expression['left'] == 'locations') {
                    $q->whereIn('city', $inValues)
                            ->orWhereIn('state', $inValues)
                            ->orWhereIn('country', $inValues);
                }
                else {
                    $q->whereIn('name', $inValues);
                }
            });
        }
        elseif (str_contains($condition, ' in ')) {
            $expression = $this->extractExpressionParts($condition, '/^(.+?)\s+(in)\s+(.+)$/i');
            $inValues = explode(',', $this->removeParentheses($expression['right']));
            $query->whereIn($expression['left'], $inValues);
        }
        else {
            $expression = $this->extractExpressionParts($condition, '/^([^!=<>\s]+)\s*(=|!=|<=|>=|<|>|like)\s*(.+)$/i');
            if (in_array($expression['left'], ['created_at', 'updated_at', 'published_at'])) {
                $query->whereDate($expression['left'], $expression['right']);
            }
            else {
                $right = $expression['operator'] == 'like' ? "%{$expression['right']}%" : $expression['right'];
                $query->where($expression['left'], $expression['operator'], $right);
            }
        }
    }

    private function splitBy($str, $dividByOperator): ?array
    {
        $str = $this->removeParentheses($str);
        $pattern = "/{$dividByOperator}(?=([^()]*(\((?1)\)[^()]*)*)$)/";
        return preg_split($pattern, $str);
    }

    private function removeParentheses($str): string
    {
        $str = trim($str);
        while ($this->isWrappedInParentheses($str)) {
            $str = trim( substr($str, 1, -1) );
        }
        return $str;
    }

    private function isWrappedInParentheses($str): bool
    {
        if (strlen($str) < 2 || $str[0] !== '(' || $str[strlen($str) - 1] !== ')') {
            return false;
        }
    
        $inner = substr($str, 1, -1);
    
        $balance = 0;
        for ($i = 0; $i < strlen($inner); $i++) {
            if ($inner[$i] === '(') $balance++;
            if ($inner[$i] === ')') $balance--;
            if ($balance < 0) return false;
        }
    
        return $balance === 0;
    }

    private function extractExpressionParts($condition, $pattern): ?array
    {
        return preg_match($pattern, $condition, $matches) ? [
            'left' => trim($matches[1]),
            'operator' => trim($matches[2]),
            'right' => trim($matches[3])
        ] : null;
    }

    private function isNumericOperator($operator): bool
    {
        return in_array($operator, ['>', '<', '>=', '<=']);
    }
}