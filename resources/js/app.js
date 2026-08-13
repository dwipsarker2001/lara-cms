import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import { mountTipTap } from './tiptap';
import { locationField, GeoLocation } from './location-picker';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.mountTipTap = mountTipTap;
window.GeoLocation = GeoLocation;
window.locationField = locationField;

Alpine.data('locationField', locationField);

Alpine.start();


