"""Error types. Messages to users must never include tokens or file contents."""


class ForgeError(Exception):
    """Base error; message is safe to show to the user."""


class ValidationError(ForgeError):
    pass


class AccessError(ForgeError):
    pass


class ExternalError(ForgeError):
    pass
