import './bootstrap';

import Alpine from 'alpinejs';
import recipeForm from "./forms/recipeForm.js";

window.recipeForm = recipeForm;
window.Alpine = Alpine;

Alpine.start();

import.meta.glob([ '../fonts/**' ]);
