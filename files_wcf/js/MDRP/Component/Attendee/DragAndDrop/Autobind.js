define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Autobind = Autobind;
    function Autobind(target, propertyKey, descriptor) {
        // Save the original method
        const originalMethod = descriptor.value;
        const adjustedDescriptor = {
            configurable: true,
            get() {
                // Bind the original method to the current instance
                const boundFn = originalMethod.bind(this);
                return boundFn;
            },
        };
        Object.defineProperty(target, propertyKey, adjustedDescriptor);
    }
});
