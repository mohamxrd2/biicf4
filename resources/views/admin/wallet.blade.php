@extends('admin.layout.navside')

@section('title', 'Porte-feuille')

@section('content')

    <livewire:wallet />


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comboBoxItems = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const agentIdInput = document.getElementById('agent_id');

            comboBoxItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    const agentId = item.getAttribute('data-hs-combo-box-output-item');
                    agentIdInput.value = agentId;
                });
            });

            const comboBoxItems2 = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const userIdInput = document.getElementById('user_id');

            comboBoxItems2.forEach(function(item) {
                item.addEventListener('click', function() {
                    const userId = item.getAttribute('data-hs-combo-box-output-item');
                    userIdInput.value = userId;
                });
            });
        });
    </script>

@endsection
