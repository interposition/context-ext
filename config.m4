PHP_ARG_ENABLE([context],
  [whether to enable context support],
  [AS_HELP_STRING([--enable-context],
    [Enable context support])],
  [no])

if test "$PHP_CONTEXT" != "no"; then
  AC_DEFINE(HAVE_CONTEXT, 1, [ Have context support ])

  PHP_NEW_EXTENSION(context, context.c terminate.c ctx.c swap_context.c init_op_array.c, $ext_shared)
fi
