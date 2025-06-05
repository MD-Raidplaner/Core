define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DragContext = void 0;
    exports.DragContext = {
        current: null,
        clear() {
            this.current = null;
        },
        get() {
            return this.current;
        },
        set(payload) {
            this.current = payload;
        },
    };
});
