import { startStimulusApp } from '@symfony/stimulus-bridge';
import Form_collection_controller from "./controllers/form_collection_controller";

export const app = startStimulusApp(require.context(
    './controllers',
    true,
    /\.(j|t)sx?$/
));
// register any custom, 3rd party controllers here
app.register('form_collection_controller', Form_collection_controller);
