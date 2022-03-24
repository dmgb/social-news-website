const requireComponent = require.context(
    './components',
    true,
    /[a-zA-Z]\.vue$/,
);

const register = (app) => {
    requireComponent.keys().forEach((fileName) => {
        const componentConfig = requireComponent(fileName);
        const componentName = fileName.split('/').pop()?.replace(/\.\w+$/, '');
        app.component(componentName, componentConfig.default || componentConfig);
    });
};

export default {
    register,
};
