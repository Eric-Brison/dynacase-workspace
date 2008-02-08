# ============================================
# $Id: Makefile,v 1.2 2008/02/08 08:04:25 eric Exp $
# $Log: Makefile,v $
# Revision 1.2  2008/02/08 08:04:25  eric
# Correct install for debian links to FDL
#
# Revision 1.1  2005/09/13 14:47:16  eric
# *** empty log message ***
#
# Revision 1.1  2001/10/03 08:55:33  eric
# liste des utilisateurs nomade
#
# Revision 1.1  2001/06/13 14:41:44  eric
# contact address book
#
# ============================================


include $(utildir)/PubRule


pages_fdl = $(patsubst %.php,$(pubdir)/$(applib)/%.php,$(wildcard Method*.php))
pages_fdl += $(patsubst %.php,$(pubdir)/$(applib)/%.php,$(wildcard Class*.php))

$(pubdir)/$(applib):
	mkdir $@

$(pubdir)/$(applib)/%.php: %.php $(pubdir)/$(applib)
	cd $(pubdir)/$(applib); \
	ln -sf ../$(appname)/$< .

publish: $(pubdir)/$(applib) $(pages_fdl) 