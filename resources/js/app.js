import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import './../../vendor/power-components/livewire-powergrid/dist/tailwind.css';
import $ from 'jquery';
import 'select2/dist/js/select2.min';
import 'select2/dist/css/select2.min.css';
import select2 from 'select2';

window.jQuery = $;
window.$ = $;
select2();

Alpine.plugin(Clipboard)

Livewire.start()

