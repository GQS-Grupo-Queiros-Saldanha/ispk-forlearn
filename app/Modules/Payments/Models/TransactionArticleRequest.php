<?php

namespace App\Modules\Payments\Models;

use App\Model;

class TransactionArticleRequest extends Model{

    protected $table = 'transaction_article_requests';

    protected $fillable = [
        'transaction_id',
        'article_request_id',
        'value',
    ];
    
     protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    
    
    public function article_request()
    {
        return $this->belongsTo(ArticleRequest::class, 'article_request_id');
    }

}