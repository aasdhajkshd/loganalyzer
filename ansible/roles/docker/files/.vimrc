" Настройки из onpremise VKTeams
au! BufNewFile,BufReadPost *.{yaml,yml} set filetype=yaml
autocmd FileType yaml setlocal ts=2 sts=2 sw=2 expandtab foldmethod=indent nofoldenable
au! BufNewFile,BufReadPost *.{pp,epp} set filetype=puppet
autocmd FileType puppet setlocal ts=2 sts=2 sw=2 expandtab foldmethod=syntax nofoldenable
au! BufNewFile,BufReadPost *.{erb} set filetype=eruby
autocmd FileType eruby setlocal ts=2 sts=2 sw=2 expandtab smarttab smartindent foldmethod=syntax nofoldenable
au! BufNewFile,BufReadPost *.{rb} set filetype=ruby
autocmd FileType ruby setlocal ts=2 sts=2 sw=2 expandtab smarttab smartindent foldmethod=syntax nofoldenable
au! BufNewFile,BufReadPost *.{py} set filetype=python
autocmd FileType python setlocal ts=4 sts=4 sw=4 expandtab smarttab smartindent foldmethod=indent nofoldenable
au! BufNewFile,BufReadPost *.{sh} set filetype=sh
autocmd FileType sh setlocal ai et ci pi sts=0 sw=4 ts=4 smarttab smartindent foldmethod=syntax nofoldenable

" Если локализация не UTF-8
scriptencoding utf-8
set encoding=utf-8

" Настроим кол-во символов пробелов, которые будут заменять \t
set tabstop=2
set shiftwidth=2
set smarttab

" включим автозамену по умолчанию
set et

" попросим Vim переносить длинные строки
set wrap

" включим автоотступы для новых строк
set ai
" включим отступы в стиле Си
set cin

" Далее настроим поиск и подсветку результатов поиска и совпадения скобок
set showmatch
set hlsearch
set incsearch
set ignorecase

" ленивая перерисовка экрана при выполнении скриптов
set lz

" Показываем табы в начале строки точками
" set listchars=tab:··
set listchars=tab:→\ ,space:·,nbsp:␣,trail:•,eol:¶,precedes:«,extends:»
set list

" Порядок применения кодировок и формата файлов

set ffs=unix,dos,mac
set fencs=utf-8,cp1251,koi8-r,ucs-2,cp866

" Взаимодействие и элементы интерфейса

" Я часто выделяю мышкой содержимое экрана в Putty, но перехват мышки в Vim мне иногда мешает. Отключаем функционал вне графического режима:
if !has('gui_running')
set mouse=
endif

" Избавляемся от меню и тулбара:
set guioptions-=T
set guioptions-=m

" Всключим подсветку синтаксиса
syntax on

" Включим копирование как есть
"set paste

" Необходимо пронумеровать
set number

" Отключение visual
set mouse-=a

