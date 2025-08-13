import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(
    import.meta.glob('./controllers/**/*_controller.js')
);
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
