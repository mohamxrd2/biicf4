<div>
    @if ($currentPage === 'wallet')
        <livewire:wallet />

    @elseif ($currentPage === 'rechargeAgent')
        <livewire:recharge-agent />
    @elseif ($currentPage === 'rechargeClient')
        <livewire:recharge-client />
    @endif
</div>
