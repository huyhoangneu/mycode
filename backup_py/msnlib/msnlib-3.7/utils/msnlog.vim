
" Vim syntax file for msnlib logfiles
" Alberto (albertito@blitiri.com.ar) 28/Sep/2003

" Use it to read your msnlib log files with color, makes it much easier.
" Install it by copying to ~/.vim/syntax/msnlog.vim and then run (from vim)
" :set syntax=msnlog to apply it.

syntax clear
hi clear
syntax case ignore


syntax match	mlogMultiStr	"^\t.*$"
syntax match	mlogIMsg	"<<< .*$"
syntax match	mlogOMsg	">>> .*$"
syntax match	mlogStatus	"\*\*\* .*$"
syntax match	mlogMchat	"+++ .*$"
syntax match	mlogRnick	"--- .*$"
syntax match	mlogDate	"^../.../.... ..:..:.."


hi mlogDate		ctermfg=blue
hi mlogIMsg		ctermfg=green
hi mlogOMsg		ctermfg=cyan
hi mlogStatus		ctermfg=yellow
hi mlogMchat		ctermfg=yellow
hi mlogRnick		ctermfg=yellow
hi mlogMultiStr		ctermfg=magenta



