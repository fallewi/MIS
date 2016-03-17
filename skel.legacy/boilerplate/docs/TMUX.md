tmux
====

tmux, the [terminal multiplexer](https://tmux.github.io/) provides lightning-fast
access to all of an environment's hosts.

using this tool we can instantaneously shell into all client servers,
share our session with another developer, run a command on all servers at once,
and get straight to work -- it places use in MAGE_ROOT.


#### requires

* tmux
* [bootstrap](https://github.com/BlueAcornInc/bootstrap) - provides [tmuxp](https://github.com/tony/tmuxp)

#### usage

start tmux, shelling into "prod" environment hosts
```sh
cd site
bin/tmux prod
```

* kill all tmux sessions with `tmux kill-session`
* kill specific session with `tmux kill-session SESSION_ID`
* list sessions with `tmux ls`
* default tmux control character is __ctrl-b__
   * e.g. pressing __ctrl-b__, then "d" will detach you from the tmux session


#### useful keys

control + key  | description
------------- | -------------
d  | detach (closes tmux). use `tmux` to re-attach later
o, or arrow keys  | switch pane
z  | zoom pane (toggles between fullscreen and layout view)
PgUp | enter scroll mode (to scroll back). Esc exits.
`:setw synchronize-panes`  | toggles synchronization between panes. Use tab completion, e.g. "`<ctrl-b> :setw syn<tab>`"
