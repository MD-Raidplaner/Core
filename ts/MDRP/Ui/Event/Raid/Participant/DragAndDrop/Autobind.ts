// Type for the method that is changed by the decorator
type Method = (...args: unknown[]) => unknown;

export function Autobind(target: unknown, propertyKey: string | symbol, descriptor: PropertyDescriptor): void {
  // Save the original method
  const originalMethod = descriptor.value as Method;

  const adjustedDescriptor: PropertyDescriptor = {
    configurable: true,
    get() {
      // Bind the original method to the current instance
      const boundFn = originalMethod.bind(this);
      return boundFn;
    },
  };

  Object.defineProperty(target, propertyKey, adjustedDescriptor);
}
