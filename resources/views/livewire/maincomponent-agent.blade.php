<div>
    @if ($currentPage === 'agent')
        <livewire:liste-agents />

    @elseif ($currentPage === 'addAgents')
        <livewire:add-agents />
    @endif
</div>
