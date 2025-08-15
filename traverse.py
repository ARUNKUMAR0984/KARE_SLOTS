# Python3 code to implement the approach

# Class describing a node of tree
class Node:
	def __init__(self, v):
		self.left = None
		self.right = None
		self.data = v

# Inorder Traversal
def printInorder(root):
	if root:
		# Traverse left subtree
		printInorder(root.left)
		
		# Visit node
		print(root.data,end=" ")
		
		# Traverse right subtree
		printInorder(root.right)

# Driver code
if __name__ == "__main__":
	# Build the tree
	root = Node(100)
	root.left = Node(20)
	root.right = Node(200)
	root.left.left = Node(10)
	root.left.right = Node(30)
	root.right.left = Node(150)
	root.right.right = Node(300)

	# Function call
	print("Inorder Traversal:",end=" ")
	printInorder(root)

	# This code is contributed by ajaymakvana.
