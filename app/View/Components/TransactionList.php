<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TransactionList extends Component
{
    public $transactions;
    public $userId;
    public $hasMoreTransactions;

    public function __construct($transactions, $userId, $hasMoreTransactions = false)
    {
        $this->transactions = $transactions;
        $this->userId = $userId;
        $this->hasMoreTransactions = $hasMoreTransactions;
    }

    public function render()
    {
        return view('components.transaction-list');
    }
}
