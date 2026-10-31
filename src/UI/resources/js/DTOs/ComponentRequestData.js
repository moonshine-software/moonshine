export class ComponentRequestData {
  constructor() {
    this._events = ''
    this._selector = ''

    this._beforeRequest = null
    this._responseHandler = null

    this._responseType = null

    this._beforeHandleResponse = null
    this._afterResponse = null

    this._errorCallback = null

    this._signal = null

    this._extraProperties = {}
  }

  get events() {
    return this._events
  }

  withEvents(value) {
    this._events = value

    return this
  }

  get selector() {
    return this._selector
  }

  withSelector(value) {
    this._selector = value

    return this
  }

  get beforeRequest() {
    return this._beforeRequest
  }

  hasBeforeRequest() {
    return this._beforeRequest !== null && this._beforeRequest
  }

  withBeforeRequest(value) {
    this._beforeRequest = value

    return this
  }

  get beforeHandleResponse() {
    return this._beforeHandleResponse
  }

  hasBeforeHandleResponse() {
    return this._beforeHandleResponse !== null && typeof this._beforeHandleResponse === 'function'
  }

  withBeforeHandleResponse(value) {
    this._beforeHandleResponse = value

    return this
  }

  get responseHandler() {
    return this._responseHandler
  }

  hasResponseHandler() {
    return this._responseHandler !== null && this._responseHandler
  }

  withResponseHandler(value) {
    this._responseHandler = value

    return this
  }

  get afterResponse() {
    return this._afterResponse
  }

  get responseType() {
    return this._responseType
  }

  hasAfterResponse() {
    return this._afterResponse !== null && typeof this._afterResponse === 'function'
  }

  withAfterResponse(value) {
    this._afterResponse = value

    return this
  }

  get errorCallback() {
    return this._errorCallback
  }

  hasErrorCallback() {
    return this._errorCallback !== null && typeof this._errorCallback === 'function'
  }

  withErrorCallback(value) {
    this._errorCallback = value

    return this
  }

  get signal() {
    return this._signal
  }

  withSignal(value) {
    this._signal = value

    return this
  }

  get extraProperties() {
    return this._extraProperties
  }

  withExtraProperties(value) {
    this._extraProperties = value

    return this
  }

  withResponseType(value) {
    this._responseType = value

    return this
  }

  fromDataset(dataset = {}) {
    return this.withEvents(dataset.asyncEvents ?? '')
      .withSelector(dataset.asyncSelector ?? '')
      .withResponseHandler(dataset.asyncResponseHandler ?? null)
      .withBeforeRequest(dataset.asyncBeforeRequest ?? null)
      .withResponseType(dataset.asyncResponseType ?? null)
  }

  fromObject(object = {}) {
    return this.withEvents(object.events ?? '')
      .withSelector(object.selector ?? '')
      .withBeforeRequest(object.beforeRequest ?? null)
      .withBeforeHandleResponse(object.beforeHandleResponse ?? null)
      .withResponseHandler(object.responseHandler ?? null)
      .withAfterResponse(object.afterResponse ?? null)
      .withErrorCallback(object.errorCallback ?? null)
      .withExtraProperties(object.extraProperties ?? null)
      .withResponseType(object.responseType ?? null)
  }
}
