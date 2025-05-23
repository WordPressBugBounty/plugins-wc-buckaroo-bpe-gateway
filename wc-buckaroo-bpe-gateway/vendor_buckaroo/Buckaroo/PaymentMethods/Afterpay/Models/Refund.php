<?php

namespace BuckarooDeps\Buckaroo\PaymentMethods\Afterpay\Models;

use BuckarooDeps\Buckaroo\Models\ServiceParameter;
use BuckarooDeps\Buckaroo\PaymentMethods\Afterpay\Service\ParameterKeys\ArticleAdapter;
use BuckarooDeps\Buckaroo\PaymentMethods\Traits\CountableGroupKey;

class Refund extends ServiceParameter
{
    use CountableGroupKey;

    /**
     * @var array|string[]
     */
    private array $countableProperties = ['articles'];

    /**
     * @var string
     */
    protected string $merchantImageUrl;

    /**
     * @var string
     */
    protected string $summaryImageUrl;

    /**
     * @var array
     */
    protected array $articles = [];

    /**
     * @var array|\string[][]
     */
    protected array $groupData = [
        'articles' => [
            'groupType' => 'Article',
        ],
    ];

    /**
     * @param array|null $articles
     * @return array
     */
    public function articles(?array $articles = null)
    {
        if (is_array($articles))
        {
            foreach ($articles as $article)
            {
                $this->articles[] = new ArticleAdapter(new Article($article));
            }
        }

        return $this->articles;
    }
}
